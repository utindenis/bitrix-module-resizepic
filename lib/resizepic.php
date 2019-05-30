<?php

namespace My\Resizepictures;

class CResizepic
{
    public static function ResizePictures(&$arFile)
    {
        var_dump($arFile);
        die;
        $MODULE_ID = basename(dirname(__DIR__));
        global $DB;
        $dbTable = 'img_resize';
        $isImage = \CFile::IsImage($arFile["name"], $arFile["type"]);
        if ($isImage) {
            $JPGfile = new \CURLFile($arFile['tmp_name'], 'image/jpeg', 'test_name');
            $target = 'https://img-resize.com/resize';
//            $error = '';
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $target);
//            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
//            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
//            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: multipart/form-data'));
//            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, array('op' => 'fixedWidth', 'width' => '15', 'input' => $JPGfile));
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            $jpg = curl_exec($ch);
//            $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
//            if ($status !== 200) {
//                $error = 'img-resize.com request failed: HTTP code ' . $status;
//                return false;
//            }
//            $curl_error = curl_error($ch);
//            if (!empty($curl_error)) {
//                $error = 'img-resize.com request failed: CURL error ' . $curl_error;
//                return false;
//            }
//            curl_close($ch);

            $uniqueIdFromJsonv2 = uniqid();

            $desktopv2 = $_SERVER["DOCUMENT_ROOT"] . "/desktop.jpg";
            $mobilev2 = $_SERVER["DOCUMENT_ROOT"] . "/mobile.jpg";
            $tabletv2 = $_SERVER["DOCUMENT_ROOT"] . "/tablet.jpg";

            $resultImg = array_merge(explode(',', $desktopv2), explode(',', $mobilev2), explode(',', $tabletv2));

            foreach ($resultImg as $item) {
                $pathForDb = "/upload/" . $MODULE_ID . "/" . substr($item, strrpos($item, "/") + 1);
                $path = $_SERVER["DOCUMENT_ROOT"] . "/upload/" . $MODULE_ID . "/" . substr($item, strrpos($item, "/") + 1);;
                $content = file_put_contents($path, file_get_contents($item));
                $DB->Query("INSERT INTO $dbTable
                    (ID, UPLOAD, UNIQ_ID)
                    VALUES ('0', '$pathForDb','$uniqueIdFromJsonv2')");
            }
        }
    }

    public static function getReiszeImageById($id)
    {

    }
}

