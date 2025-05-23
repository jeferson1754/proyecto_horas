<?php
// Configuración de visualización de errores para desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuración de zona horaria
date_default_timezone_set('America/Lima'); // Ajusta según tu zona horaria

/**
 * Clase para manejar el sistema de registro de asistencia
 */
class AttendanceSystem
{
    private $imap_connection;
    private $db_connection;
    private $results = [];

    // Configuración IMAP
    private $imap_server = "{imap.gmail.com:993/imap/ssl}";

    private $email = "correo@gmail.com";
    private $password = "1234";

    // Configuración Base de Datos
    private $db_host = "localhost";
    private $db_user = "root";
    private $db_pass = "";
    private $db_name = "epiz_32740026_r_user";

    public function __construct()
    {
        $this->results['start_time'] = date('Y-m-d H:i:s');
        $this->results['processed_emails'] = 0;
        $this->results['successful_records'] = 0;
        $this->results['errors'] = [];
        $this->results['details'] = [];
    }

    /**
     * Conecta al servidor IMAP
     */
    private function connectIMAP()
    {
        try {
            $label = imap_utf7_encode("Registro de Asistencia");
            $this->imap_connection = imap_open(
                $this->imap_server . $label,
                $this->email,
                $this->password
            );

            if (!$this->imap_connection) {
                throw new Exception("Error de conexión IMAP: " . imap_last_error());
            }

            $this->addResult('success', '✅ Conexión IMAP establecida correctamente');
            return true;
        } catch (Exception $e) {
            $this->addResult('error', '❌ ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Conecta a la base de datos MySQL
     */
    private function connectDatabase()
    {
        try {
            $this->db_connection = new mysqli(
                $this->db_host,
                $this->db_user,
                $this->db_pass,
                $this->db_name
            );

            if ($this->db_connection->connect_error) {
                throw new Exception("Error de conexión a BD: " . $this->db_connection->connect_error);
            }

            // Configurar charset UTF-8
            $this->db_connection->set_charset("utf8");

            $this->addResult('success', '✅ Conexión a base de datos establecida');
            return true;
        } catch (Exception $e) {
            $this->addResult('error', '❌ ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpia texto con codificación quoted-printable
     */
    private function cleanQuotedPrintable($text)
    {
        $text = preg_replace('/=\s*/', '', $text);
        $text = quoted_printable_decode($text);
        return trim($text);
    }

    /**
     * Busca el ID de una fecha en la tabla horas
     */
    private function findDateId($date)
    {
        try {
            $stmt = $this->db_connection->prepare("SELECT ID FROM horas WHERE Dia = ?");
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db_connection->error);
            }

            $stmt->bind_param("s", $date);
            $stmt->execute();

            $id = null;
            $stmt->bind_result($id);

            if ($stmt->fetch()) {
                $stmt->close();
                return $id;
            } else {
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            $this->addResult('error', "Error buscando fecha: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Procesa un email de registro de asistencia
     */
    private function processEmail($email_num)
    {
        try {
            $overview = imap_fetch_overview($this->imap_connection, $email_num, 0)[0];
            $structure = imap_fetchstructure($this->imap_connection, $email_num);
            $subject = $overview->subject ?? '';

            // Verificar si es un email de Revesol
            if (stripos($subject, "recinto: Revesol") === false) {
                return false;
            }

            $this->results['processed_emails']++;

            // Determinar tipo de evento
            $event_type = stripos($subject, "salida") !== false ? "Salida" : "Ingreso";

            // Obtener y decodificar el cuerpo del mensaje
            $message = imap_fetchbody($this->imap_connection, $email_num, 1);
            $encoding = $structure->parts[0]->encoding ?? 0;

            if ($encoding == 3) {
                $message = base64_decode($message);
            } elseif ($encoding == 4) {
                $message = quoted_printable_decode($message);
            }

            // Extraer datos del HTML
            $data = $this->extractDataFromHTML($message);

            if ($data['hour'] && $data['date']) {
                $this->saveAttendanceRecord($event_type, $data['hour'], $data['date']);
                return true;
            } else {
                $this->addResult('warning', "⚠️ No se pudieron extraer datos del email: " . substr($subject, 0, 50));
                return false;
            }
        } catch (Exception $e) {
            $this->addResult('error', "Error procesando email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Extrae hora y fecha del contenido HTML del email
     */
    private function extractDataFromHTML($html_content)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html_content);
        libxml_clear_errors();

        $tds = $dom->getElementsByTagName('td');
        $hour = null;
        $date = null;

        for ($i = 0; $i < $tds->length; $i++) {
            $td = $tds->item($i);
            $text = trim($td->textContent);

            if (stripos($text, 'Hora:') !== false) {
                $nextTd = $tds->item($i + 1);
                if ($nextTd) {
                    $hour = $this->cleanQuotedPrintable(trim($nextTd->textContent));
                }
            }

            if (stripos($text, 'Fecha:') !== false) {
                $nextTd = $tds->item($i + 1);
                if ($nextTd) {
                    $date = $this->cleanQuotedPrintable(trim($nextTd->textContent));
                }
            }
        }

        // Formatear fecha de DD-MM-YYYY a YYYY-MM-DD
        $formatted_date = null;
        if (preg_match('/(\d{2})-(\d{2})-(\d{4})/', $date, $matches)) {
            $formatted_date = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        // Validar formato de hora HH:MM:SS
        $formatted_hour = null;
        if (preg_match('/(\d{2}):(\d{2}):(\d{2})/', $hour, $matches)) {
            $formatted_hour = $matches[0];
        }

        return [
            'hour' => $formatted_hour,
            'date' => $formatted_date
        ];
    }

    /**
     * Crea las columnas necesarias para los cálculos si no existen
     */
    private function createCalculationColumns()
    {
        try {
            $columns = [
                "`Total Horas` TIME DEFAULT '00:00:00'",
                "`Total Colacion` TIME DEFAULT '00:00:00'",
                "`Horas Finales` TIME DEFAULT '00:00:00'"
            ];

            foreach ($columns as $column) {
                $sql = "ALTER TABLE horas ADD COLUMN $column";
                if ($this->db_connection->query($sql)) {
                    $this->addResult('success', "✅ Columna creada: $column");
                } else {
                    // Si la columna ya existe, MySQL devuelve error, pero lo ignoramos
                    if (strpos($this->db_connection->error, 'Duplicate column name') === false) {
                        $this->addResult('warning', "⚠️ Error creando columna $column: " . $this->db_connection->error);
                    }
                }
            }
        } catch (Exception $e) {
            $this->addResult('error', "Error creando columnas: " . $e->getMessage());
        }
    }

    /**
     * Calcula las horas trabajadas con colación automática
     */
    private function calculateWorkingHours($ingreso, $salida)
    {
        $calculations = [
            'total_horas' => '00:00:00',
            'total_colacion' => '00:00:00',
            'horas_finales' => '00:00:00',
            'segundos_totales' => 0,
            'segundos_colacion' => 0,
            'segundos_finales' => 0
        ];

        if (!$ingreso || !$salida) {
            return $calculations;
        }

        try {
            // Calcular Total Horas (Ingreso a Salida)
            $segundosTotalHoras = strtotime($salida) - strtotime($ingreso);
            $calculations['segundos_totales'] = $segundosTotalHoras;
            $calculations['total_horas'] = gmdate("H:i:s", $segundosTotalHoras);

            // Horarios fijos de colación
            $colacion = "14:20:00";
            $fin_colacion = "15:00:00";

            // Calcular Total Colación (siempre 40 minutos = 2400 segundos)
            $segundosTotalColacion = strtotime($fin_colacion) - strtotime($colacion);
            $calculations['segundos_colacion'] = $segundosTotalColacion;
            $calculations['total_colacion'] = gmdate("H:i:s", $segundosTotalColacion);

            // Calcular Horas Finales (Total Horas - Total Colación)
            $segundosHorasFinales = $segundosTotalHoras - $segundosTotalColacion;
            $calculations['segundos_finales'] = max(0, $segundosHorasFinales);
            $calculations['horas_finales'] = gmdate("H:i:s", max(0, $segundosHorasFinales));

            return $calculations;
        } catch (Exception $e) {
            $this->addResult('error', "Error calculando horas: " . $e->getMessage());
            return $calculations;
        }
    }

    /**
     * Guarda o actualiza el registro de asistencia
     */
    private function saveAttendanceRecord($event_type, $hour, $date)
    {
        try {
            if ($event_type == 'Ingreso') {
                $date_id = $this->findDateId($date);

                if ($date_id === false) {
                    // Crear nuevo registro
                    $sql = "INSERT INTO horas (Dia, `Hora Ingreso`) VALUES (?, ?)";
                    $stmt = $this->db_connection->prepare($sql);
                    $stmt->bind_param("ss", $date, $hour);

                    if ($stmt->execute()) {
                        $this->results['successful_records']++;
                        $this->addResult('success', "✅ Ingreso registrado: $date $hour");
                    } else {
                        throw new Exception("Error insertando ingreso: " . $this->db_connection->error);
                    }
                    $stmt->close();
                } else {
                    // Actualizar registro existente
                    $sql = "UPDATE horas SET `Hora Ingreso` = ? WHERE ID = ?";
                    $stmt = $this->db_connection->prepare($sql);
                    $stmt->bind_param("si", $hour, $date_id);

                    if ($stmt->execute()) {
                        $this->results['successful_records']++;
                        $this->addResult('success', "✅ Ingreso actualizado: $date $hour (ID: $date_id)");
                    } else {
                        throw new Exception("Error actualizando ingreso: " . $this->db_connection->error);
                    }
                    $stmt->close();
                }
            } else {
                // Salida - Calcular horas trabajadas
                $date_id = $this->findDateId($date);

                if ($date_id !== false) {
                    // Verificar que las columnas existan en la tabla
                    $check_columns = $this->db_connection->query("SHOW COLUMNS FROM horas LIKE 'Total Horas'");
                    if ($check_columns->num_rows == 0) {
                        $this->addResult('warning', "⚠️ Las columnas de cálculo no existen. Creando columnas...");
                        $this->createCalculationColumns();
                    }

                    // Obtener la hora de ingreso para calcular las horas trabajadas
                    $stmt = $this->db_connection->prepare("SELECT `Hora Ingreso` FROM horas WHERE ID = ?");

                    if (!$stmt) {
                        throw new Exception("Error preparando consulta SELECT: " . $this->db_connection->error);
                    }

                    $stmt->bind_param("i", $date_id);
                    $stmt->execute();

                    $ingreso = null;
                    $stmt->bind_result($ingreso);
                    $stmt->fetch();
                    $stmt->close();

                    // Calcular horas trabajadas
                    $calculations = $this->calculateWorkingHours($ingreso, $hour);

                    $colacion = "14:20:00";
                    $fin_colacion = "15:00:00";


                    // Actualizar registro con salida y cálculos
                    $sql = "UPDATE horas SET 
                            `Hora Salida` = ?, 
                            `Total Horas` = ?, 
                            `Hora Colacion` = ?, 
                            `Hora Fin Colacion` = ?, 
                            `Total Colacion` = ?, 
                            `Horas Final` = ? 
                            WHERE ID = ?";

                    $stmt = $this->db_connection->prepare($sql);

                    if (!$stmt) {
                        throw new Exception("Error preparando consulta UPDATE: " . $this->db_connection->error);
                    }

                    if (!$stmt->bind_param(
                        "ssssssi",
                        $hour,
                        $calculations['total_horas'],
                        $colacion,
                        $fin_colacion,
                        $calculations['total_colacion'],
                        $calculations['horas_finales'],
                        $date_id
                    )) {
                        throw new Exception("Error en bind_param: " . $stmt->error);
                    }

                    if ($stmt->execute()) {
                        $this->results['successful_records']++;
                        $this->addResult(
                            'success',
                            "✅ Registro completo - Fecha: $date | " .
                                "Ingreso: $ingreso | Salida: $hour | " .
                                "Horas Trabajadas: {$calculations['horas_finales']} | " .
                                "Colación: {$calculations['total_colacion']}"
                        );
                    } else {
                        throw new Exception("Error registrando salida: " . $this->db_connection->error);
                    }
                    $stmt->close();
                } else {
                    // Actualizar solo la hora de salida si las columnas no existen
                    $sql = "UPDATE horas SET `Hora Salida` = ? WHERE ID = ?";
                    $stmt = $this->db_connection->prepare($sql);

                    if (!$stmt) {
                        throw new Exception("Error preparando consulta simple UPDATE: " . $this->db_connection->error);
                    }

                    if (!$stmt->bind_param("si", $hour, $date_id)) {
                        throw new Exception("Error en bind_param simple: " . $stmt->error);
                    }

                    if ($stmt->execute()) {
                        $this->results['successful_records']++;
                        $this->addResult('success', "✅ Salida registrada: $date $hour (ID: $date_id)");
                        $this->addResult('info', "ℹ️ Cálculo de horas disponible después de crear columnas");
                    } else {
                        throw new Exception("Error registrando salida simple: " . $this->db_connection->error);
                    }
                    $stmt->close();
                }
            }
        } catch (Exception $e) {
            $this->addResult('error', "❌ " . $e->getMessage());
        }
    }

    /**
     * Añade un resultado al log
     */
    private function addResult($type, $message)
    {
        $this->results['details'][] = [
            'type' => $type,
            'message' => $message,
            'timestamp' => date('H:i:s')
        ];

        if ($type == 'error') {
            $this->results['errors'][] = $message;
        }
    }

    /**
     * Procesa todos los emails no leídos
     */
    public function processUnreadEmails()
    {
        if (!$this->connectIMAP() || !$this->connectDatabase()) {
            return $this->getResults();
        }

        try {
            $emails = imap_search($this->imap_connection, 'UNSEEN');

            if ($emails) {
                $this->addResult('info', "📧 Encontrados " . count($emails) . " emails no leídos");

                foreach ($emails as $email_num) {
                    $this->processEmail($email_num);
                }
            } else {
                $this->addResult('info', "📭 No hay emails nuevos para procesar");
            }
        } catch (Exception $e) {
            $this->addResult('error', "Error procesando emails: " . $e->getMessage());
        }

        return $this->getResults();
    }

    /**
     * Obtiene los resultados del procesamiento
     */
    public function getResults()
    {
        $this->results['end_time'] = date('Y-m-d H:i:s');
        $this->results['execution_time'] = strtotime($this->results['end_time']) - strtotime($this->results['start_time']);

        return $this->results;
    }

    /**
     * Cierra las conexiones
     */
    public function __destruct()
    {
        if ($this->imap_connection) {
            imap_close($this->imap_connection);
        }
        if ($this->db_connection) {
            $this->db_connection->close();
        }
    }
}

/**
 * Función para mostrar los resultados con formato HTML
 */
function displayResults($results)
{
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistema de Registro de Asistencia</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 20px;
                min-height: 100vh;
            }

            .container {
                max-width: 1000px;
                margin: 0 auto;
                background: white;
                border-radius: 15px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .header {
                background: linear-gradient(135deg, #2c3e50, #3498db);
                color: white;
                padding: 30px;
                text-align: center;
            }

            .header h1 {
                margin: 0;
                font-size: 2.5em;
                font-weight: 300;
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                padding: 30px;
                background: #f8f9fa;
            }

            .stat-card.hours {
                border-left-color: #28a745;
            }

            .stat-card.lunch {
                border-left-color: #ffc107;
            }

            .stat-card.final {
                border-left-color: #17a2b8;
            }

            .stat-number {
                font-size: 2em;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .stat-label {
                color: #7f8c8d;
                font-size: 0.9em;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .results-section {
                padding: 30px;
            }

            .result-item {
                display: flex;
                align-items: center;
                padding: 15px;
                margin-bottom: 10px;
                border-radius: 8px;
                border-left: 4px solid;
                transition: transform 0.2s ease;
            }

            .result-item:hover {
                transform: translateX(5px);
            }

            .success {
                background: #d4edda;
                border-color: #28a745;
                color: #155724;
            }

            .error {
                background: #f8d7da;
                border-color: #dc3545;
                color: #721c24;
            }

            .warning {
                background: #fff3cd;
                border-color: #ffc107;
                color: #856404;
            }

            .info {
                background: #d1ecf1;
                border-color: #17a2b8;
                color: #0c5460;
            }

            .timestamp {
                margin-left: auto;
                font-size: 0.8em;
                opacity: 0.7;
                font-family: monospace;
            }

            .no-results {
                text-align: center;
                padding: 40px;
                color: #6c757d;
                font-style: italic;
            }

            .footer {
                background: #2c3e50;
                color: white;
                padding: 20px;
                text-align: center;
                font-size: 0.9em;
            }

            .volver-btn {
                position: absolute;
                top: 10px;
                left: 10px;
                padding: 10px 20px;
                background-color: rgb(79, 83, 86);
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-size: 16px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: background-color 0.3s ease;
            }

            .volver-btn:hover {
                background-color: rgb(196, 199, 202);
                color: black;
            }
        </style>
    </head>

    <body>
        <div class="container">

            <a href="./" class="volver-btn">Volver</a>
            <div class="header">
                <h1>📊 Sistema de Registro de Asistencia</h1>
                <p>Procesamiento automático de emails de asistencia</p>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $results['processed_emails']; ?></div>
                    <div class="stat-label">📧 Emails Procesados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $results['successful_records']; ?></div>
                    <div class="stat-label">✅ Registros Exitosos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($results['errors']); ?></div>
                    <div class="stat-label">❌ Errores</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $results['execution_time']; ?>s</div>
                    <div class="stat-label">⏱️ Tiempo de Ejecución</div>
                </div>
                <div class="stat-card hours">
                    <div class="stat-number">14:20-15:00</div>
                    <div class="stat-label">🍽️ Horario Colación</div>
                </div>
                <div class="stat-card lunch">
                    <div class="stat-number">40 min</div>
                    <div class="stat-label">⏰ Tiempo Colación</div>
                </div>
            </div>

            <div class="results-section">
                <h2>📋 Detalles del Procesamiento</h2>

                <?php if (empty($results['details'])): ?>
                    <div class="no-results">
                        No hay detalles para mostrar
                    </div>
                <?php else: ?>
                    <?php foreach ($results['details'] as $detail): ?>
                        <div class="result-item <?php echo $detail['type']; ?>">
                            <span><?php echo htmlspecialchars($detail['message']); ?></span>
                            <span class="timestamp"><?php echo $detail['timestamp']; ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="footer">
                Iniciado: <?php echo $results['start_time']; ?> |
                Finalizado: <?php echo $results['end_time']; ?> |
                Sistema desarrollado para Jeferson Vargas
            </div>
        </div>
    </body>

    </html>
<?php
}

// Ejecutar el sistema
try {
    $system = new AttendanceSystem();
    $results = $system->processUnreadEmails();
    displayResults($results);
} catch (Exception $e) {
    echo "<div style='color: red; padding: 20px; text-align: center;'>";
    echo "<h2>❌ Error Fatal</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>