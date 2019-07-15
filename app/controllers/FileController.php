<?php

namespace app\controllers;

use core\Controller;
use core\DB;
use PDO;

class FileController extends Controller
{
    public function index()
    {
        $this->view->render('/file/index.php');
    }

    public function upload()
    {
        if (isset($_POST['countOfUploads']) && is_numeric($_POST['countOfUploads']) &&  $_POST['countOfUploads'] > 0 && $_POST['countOfUploads'] < 5) {
            return $this->view->render('/file/upload.php', $_POST['countOfUploads']);
        }
        header('Location: /file');
    }

    public function saveFiles()
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        try {
            $files = $this->validateFiles();
            $files = $this->uploadFiles($files);
            $description = $this->validateDescription();

            $db = DB::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO images (`image`, `description`) VALUES (:image, :description)");

            foreach ($files as $key => $fileName) {
                $data = [
                    'image' => $fileName,
                    'description' => ($description[$key] !== "") ? $description[$key] : 'some image'
                ];
                $stmt->execute($data);
            }
        } catch (\Exception $e) {
            echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
            die();
        }

        echo json_encode(['result' => 'success']);
        die();
    }

    private function validateFiles()
    {

        if (!isset($_FILES['fileToUpload']) || !is_array($_FILES['fileToUpload']['name'])) {
            throw new \Exception('No files');
        }

        $files = [];

        foreach ($_FILES["fileToUpload"]["error"] as $key => $error) {
            if (UPLOAD_ERR_OK !== $error) {
                continue;
            }
            if (!in_array($_FILES['fileToUpload']['type'][$key], ['image/jpeg', 'image/jpg'], false)){
                throw new \Exception("Files {$_FILES['fileToUpload']['name'][$key]} has wrong format");
            }

            if ($_FILES['fileToUpload']['size'][$key] > 100000) {
                throw new \Exception( "File {$_FILES['fileToUpload']['name'][$key]} is too large. File must be less than  100 kb.");
            }
            if (file_exists( $this->getUploadDir() . basename($_FILES['fileToUpload']['name'][$key]))) {
                throw new \Exception('File already exists - ' . $_FILES['fileToUpload']['name'][$key]);
            }

            $files[$key] = [
                'name' => $_FILES['fileToUpload']['name'][$key],
                'tmp' => $_FILES['fileToUpload']['tmp_name'][$key]
            ];
        }

        return $files;

    }

    private function getUploadDir()
    {
        return 'uploads/';
    }

    private function uploadFiles($files)
    {
        $uploaded = [];
        foreach ($files as $key => $file) {

            $uploadFile = $this->getUploadDir() . basename($file['name']);

            if (!move_uploaded_file($file['tmp'], $uploadFile)) {
                if (!empty($uploaded)) {
                    foreach ($uploaded as $uploadedFile) {
                        @unlink($this->getUploadDir() . $uploadedFile);
                    }
                }
                throw new \Exception('Cant load file ' . $file);
            }
            $uploaded[$key] = $file['name'];
        }
        return $uploaded;
    }

    private function validateDescription()
    {
        $result = [];
        if (empty($_POST['description'])) {
            return $result;
        }

        foreach ($_POST['description'] as $description) {
            $result[] = substr(trim(htmlspecialchars($description)), 0, 60);
        }

        return $result;

    }

    public function showFiles()
    {
        $db = DB::getInstance()->getConnection();
        $data = $db->query("SELECT id, description, image FROM images ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $this->view->render('/file/show.php', $data);
    }

    public function removeFile()
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        try {
            if (!isset($_POST['fileId']) || !is_numeric($_POST['fileId'])) {
                throw new \Exception('Id not set');
            }
            $db = DB::getInstance()->getConnection();
            $fileData = $db->query("SELECT id, description, image FROM images Where id = " . (int) $_POST['fileId'])->fetch();
            if (empty($fileData)) {
                throw new \Exception('File not Found');
            }

            $stmt = $db->prepare("DELETE FROM images WHERE id = :id");
            $stmt->bindParam(':id', $_POST['fileId'], PDO::PARAM_INT);
            $stmt->execute();

            @unlink($this->getUploadDir() . $fileData['image']);
        } catch (\Exception $e) {
            echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
            die();
        }

        echo json_encode(['result' => 'success']);
        die();

    }
}