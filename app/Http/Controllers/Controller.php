<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

use Illuminate\Support\Facades\Cache;


use Ifsnop\Mysqldump;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  public $cacheDuration = 60 * 60 * 24 * 14; //14 days

  protected function logAction($action)
  {
    return;
  }

  protected function addSyncer($action, $tableName, $id)
  {
  }

  protected function createNotification($message, $recipient, $showDate)
  {
  }

  protected function composeTextEmail($email, $subject, $message)
  {

    $mail = new PHPMailer(true);

    $response = NULL;

    try {

      $mail->SMTPDebug = 0;
      $mail->isSMTP();
      $mail->Host = env('PHPMAILER_EMAIL_HOST');
      $mail->SMTPAuth = true;
      $mail->Username = env('PHPMAILER_EMAIL_USERNAME');
      $mail->Password = env('PHPMAILER_EMAIL_PASSWORD');
      $mail->SMTPSecure = env('PHPMAILER_SMTP_SECURE_OPTION');
      $mail->Port = env('PHPMAILER_EMAIL_PORT');

      $mail->setFrom(env('PHPMAILER_EMAIL_USERNAME'), env('APP_NAME'));
      $mail->addAddress($email);


      $mail->isHTML(true);                // Set email content format to HTML

      $mail->Subject = $subject;
      $mail->Body    = $message;

      if (!$mail->send()) {
        $response = array(
          'status' => false,
          'info' => $mail->ErrorInfo,
        );
      } else {
        $response = array(
          'status' => true,
          'info' => NULL
        );
      }
    } catch (Exception $e) {
      Log::error($e->getMessage());
      $response = array(
        'status' => false,
        'info' => $e->getMessage(),
      );
    }

    return $response;
  }

  public function backup()
  {
    $dbHost = env('DB_HOST');
    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');
    $dbName = env('DB_DATABASE');

    try {
      $dump = new Mysqldump\Mysqldump('mysql:host=' . $dbHost . ';dbname=' . $dbName, $username, $password);
      $time = date('Y-M-d H:i:sa', time());
      $dump->start('./sql-backups/' . $time . '-backup.sql');
      echo "DB dump backup successful";
    } catch (\Exception $e) {
      Log::error($e->getMessage());
    }
  }

  public function emailSecrets()
  {
    try {
      //code...
      $file = fopen(storage_path("app/email.txt"), "r");
    } catch (\Throwable $th) {
      $newFile = fopen(storage_path("app/email.txt"), "w");
      $newJSON = array(
        'user_name' => "testusser",
        'password' => "testpasword",
        'host' => "testhost",
        'secure_option' => "testsecureoption",
        'email_port' => "testport"
      );
      $string = base64_encode(json_encode($newJSON));
      fwrite($newFile, $string);
    } finally {
      $file = fopen(storage_path("app/email.txt"), "r");
    }

    $fileContent = '';
    while (!feof($file)) {
      $fileContent = fgets($file);
    }

    $data = json_decode(base64_decode($fileContent), true);

    return view('system.env.index', [
      'data' => $data,
    ]);
  }

  public function updateSecrets(Request $request)
  {
    $request->validate([
      'PHPMAILER_EMAIL_USERNAME' => 'required',
      'PHPMAILER_EMAIL_PASSWORD' => 'required',
      'PHPMAILER_EMAIL_HOST' => 'required',
      'PHPMAILER_SMTP_SECURE_OPTION' => 'required',
      'PHPMAILER_EMAIL_PORT' => 'required',
    ]);

    $newJSON = array(
      'user_name' => $request->PHPMAILER_EMAIL_USERNAME,
      'password' => $request->PHPMAILER_EMAIL_PASSWORD,
      'host' => $request->PHPMAILER_EMAIL_HOST,
      'secure_option' => $request->PHPMAILER_SMTP_SECURE_OPTION,
      'email_port' => $request->PHPMAILER_EMAIL_PORT
    );

    $newFile = fopen(storage_path("app/email.txt"), "w");
    $data = base64_encode(json_encode($newJSON));
    fwrite($newFile, json_encode($data));

    return redirect('/email-secrets')->with('Success', 'Your updates have been done');
  }

  protected function groupBy($array, $key)
  {
    $return = array();
    foreach ($array as $value) {
      $return[$value[$key]][] = $value;
    }
    return $return;
  }

  protected function uploadFile($file, $folder)
  {
    $path = $file->store($folder);

    return $path;
  }

  public function flushCache()
  {
    Cache::flush();
    return back();
  }
}
