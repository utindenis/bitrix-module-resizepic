<?php

namespace My\Resizepictures;

/**
 * Class CResizepic
 * @package My\Resizepictures
 */
class CResizepic
{

    /**
     * Getting local path
     * @param $path
     * @return mixed
     */
    public static function GetLocalPath($path) {
        if(strpos($path, $_SERVER['DOCUMENT_ROOT'])===0) {
            $path = str_replace($_SERVER['DOCUMENT_ROOT'], "", $path);
        }
        return $path;
    }

    /**
     * Resizing pictures
     * @param $arFile
     */
    public static function ResizePictures(&$arFile) {
        $MODULE_ID = basename(dirname(__DIR__));
        global $DB;
        $table = 'img_resize';
        $isImage = \CFile::IsImage($arFile["name"], $arFile["type"]);
        if ($isImage) {
            /**
             * @todo Переделать для внутреннего сервиса
             */
//            $JPGfile = new \CURLFile($arFile['tmp_name'], mime_content_type($arFile['tmp_name']), 'test_name');
//            $target = 'https://img-resize.com/resize';
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
//
//        $json = file_get_contents('http://idemcloud.ru/optimize?url=http://imperium.intrumnet.com/files/crm/product/c9/54/5cb3a10beda1b.jpg&desktop=1920&tablet=1024&mobile=768');
//        $obj = json_decode($json, true);
////        $uniqueId = uniqid();
//        $uniqueIdFromJson = $obj['_id'];

//            $desktop = file_get_contents($obj['desktop']['img']);
//            $mobile = file_get_contents($obj['mobile']['img']);
//            $tablet = file_get_contents($obj['tablet']['img']);

            $uniqueIdFromJsonv2 = uniqid();

            $desktopv2['desktop']['img'] = $_SERVER["DOCUMENT_ROOT"] . "/desktop.jpg";
            $desktopv2['desktop']['webp'] = $_SERVER["DOCUMENT_ROOT"] . "/desktop.jpg";
            $mobilev2['tablet']['img'] = $_SERVER["DOCUMENT_ROOT"] . "/mobile.jpg";
            $mobilev2['tablet']['webp'] = $_SERVER["DOCUMENT_ROOT"] . "/mobile.jpg";
            $tabletv2['mobile']['img'] = $_SERVER["DOCUMENT_ROOT"] . "/tablet.jpg";
            $tabletv2['mobile']['webp'] = $_SERVER["DOCUMENT_ROOT"] . "/tablet.jpg";

            $resultImg = array_merge($desktopv2, $mobilev2, $tabletv2);

            foreach ($resultImg as $item => $value) {
                foreach ($value as $link) {
                    if ($item == 'desktop') {
                        $device = 1;
                    } elseif ($item == 'tablet') {
                        $device = 2;
                    } elseif ($item == 'mobile') {
                        $device = 3;
                    }
                    $path = $_SERVER["DOCUMENT_ROOT"] . "/upload/" . $MODULE_ID . "/" . substr($link, strrpos($link, "/") + 1);
                    $pathForDb = self::GetLocalPath($path);
                    $content = file_put_contents($path, file_get_contents($link));
                    $strSQL = "INSERT INTO $table
                    (ID, UPLOAD, UNIQ_ID, DEVICE)
                    VALUES ('0', '$pathForDb','$uniqueIdFromJsonv2', '$device')";
                    $DB->Query($strSQL);
                }
            }
        }
    }

    /**
     * Getting pictures by ID
     * @param $id
     */
    public static function GetResizedImageById($id) {
        global $DB;
        $table = 'img_resize';
        $strSQL = sprintf("SELECT UPLOAD, DEVICE FROM `%s` WHERE UNIQ_ID = '$id'",
            $table);
        $results = $DB->Query($strSQL);
        while ($row = $results->Fetch()) {
            $link[] = $row['UPLOAD'];
            $device[] = $row['DEVICE'];
        }
        $arImg = array_combine($link, $device);

        $img = [
            'desktop' => [],
            'tablet' => [],
            'mobile' => []
        ];

        $superKeys = [
            1 => 'desktop',
            2 => 'tablet',
            3 => 'mobile',
        ];

        foreach ($arImg as $value => $items) {
            if (isset($superKeys[$items])) {
                $subkey = 'webp';
                if (0 === count($img[$superKeys[$items]])) {
                    $subkey = 'img';
                }
                $img[$superKeys[$items]][$subkey] = $value;
            }
        }
        return (json_encode($img));
    }
}

