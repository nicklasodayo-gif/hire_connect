<?php

require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require __DIR__ . '/smtp_config.php';

/*
|--------------------------------------------------------------------------
| Create PHPMailer Instance
|--------------------------------------------------------------------------
*/

function getMailer()
{
    global $config;

    $mail = new PHPMailer(true);

    // Enable SMTP Debugging (Remove after testing)
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    $mail->isSMTP();

    $mail->Host = $config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['username'];
    $mail->Password = $config['password'];

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int)$config['port'];

    $mail->setFrom(
        $config['from_email'],
        $config['from_name']
    );

    $mail->isHTML(true);
    $mail->CharSet = "UTF-8";

    return $mail;
}

/*
|--------------------------------------------------------------------------
| Send Interview Invitation
|--------------------------------------------------------------------------
*/

function sendInterviewEmail(
    $email,
    $name,
    $date,
    $time,
    $type,
    $venue = "",
    $meetingLink = ""
) {

    try {

        $mail = getMailer();

        $mail->addAddress($email, $name);

        $mail->Subject = "Interview Invitation - HireConnect";

        /* Venue Row */

        $venueRow = "";

        if (!empty($venue)) {

            $venueRow = "

            <tr>

                <th align='left'>Venue</th>

                <td>{$venue}</td>

            </tr>

            ";

        }

        /* Meeting Link Row */

        $meetingRow = "";

        if (strtolower($type) == "online" && !empty($meetingLink)) {

            $meetingRow = "

            <tr>

                <th align='left'>Meeting Link</th>

                <td>

                    <a href='{$meetingLink}'>

                        Join Interview

                    </a>

                </td>

            </tr>

            ";

        }

        $mail->Body = "

        <div style='font-family:Arial,sans-serif;font-size:15px;'>

            <h2>Hello {$name},</h2>

            <p>

                Congratulations!

            </p>

            <p>

                You have been shortlisted and invited for an interview.

            </p>

            <table
                border='1'
                cellpadding='10'
                cellspacing='0'
                style='border-collapse:collapse;'>

                <tr>

                    <th align='left'>Date</th>

                    <td>{$date}</td>

                </tr>

                <tr>

                    <th align='left'>Time</th>

                    <td>{$time}</td>

                </tr>

                <tr>

                    <th align='left'>Interview Type</th>

                    <td>{$type}</td>

                </tr>

                {$venueRow}

                {$meetingRow}

            </table>

            <br>

            <p>

                Please arrive at least 10 minutes early.

            </p>

            <p>

                If you cannot attend, kindly notify the employer.

            </p>

            <br>

            <strong>

                HireConnect Recruitment Team

            </strong>

        </div>

        ";

        $mail->AltBody =

"Hello {$name},

Congratulations!

You have been invited for an interview.

Date: {$date}
Time: {$time}
Interview Type: {$type}
Venue: {$venue}
Meeting Link: {$meetingLink}

Regards,

HireConnect Recruitment Team";

        $mail->send();

        return true;

    } catch (Exception $e) {

        echo "<h3>PHPMailer Error</h3>";

        echo "<strong>Error:</strong><br>";

        echo $mail->ErrorInfo . "<br><br>";

        echo "<strong>Exception:</strong><br>";

        echo $e->getMessage();

        return false;

    }

}