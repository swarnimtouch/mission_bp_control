<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class BannerController extends Controller
{
    public function index()
    {
        return view('banner.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'doctor_name' => 'required',
            'speciality' => 'required',
            'date' => 'required',
            'time' => 'required',
            'cropped_image' => 'required'
        ]);

        $doctorName = $request->doctor_name;
        $speciality = $request->speciality;
        $date = $request->date;
        $time = $request->time;

        // ✅ BASE64 IMAGE DECODE
        $imageData = str_replace('data:image/png;base64,', '', $request->cropped_image);
        $imageData = base64_decode($imageData);
        $doctorImg = imagecreatefromstring($imageData);

        // ✅ LOAD JPG BACKGROUNDS
        $bg1 = imagecreatefromjpeg(public_path('images/1.jpg'));
        $bg2 = imagecreatefromjpeg(public_path('images/2.jpg'));

        // =========================
        // 🧑‍⚕️ DOCTOR PHOTO SETTINGS
        // =========================
        $photoWidth = 457;
        $photoHeight = 457;

        $doctorResized = imagescale($doctorImg, $photoWidth, $photoHeight);

        // exact photoshop position
        imagecopy($bg1, $doctorResized, 1396, 177, 0, 0, $photoWidth, $photoHeight);
        imagecopy($bg2, $doctorResized, 1396, 177, 0, 0, $photoWidth, $photoHeight);

        // =========================
        // 🎨 COLORS & FONT
        $lightBlue = imagecolorallocate($bg1, 80, 130, 190);  // light text
        // =========================
        $blue = imagecolorallocate($bg1, 34, 84, 147);
        // =========================
        $lineColor = imagecolorallocate($bg1, 0, 0, 0);       // line
        $white = imagecolorallocate($bg1, 255, 255, 255);  // main blue
        $dateText = date('jS F Y', strtotime($date));

        $font = public_path('fonts/Poppins-Bold.ttf');
        $fontRegular = public_path('fonts/Poppins-Regular.ttf');

        // =========================
        // 🔠 FONT SIZES
        // =========================
        $maxNameWidth = 400;

        $nameSize = $this->fitTextToWidth($doctorName, $font, $maxNameWidth, 25);

        $specSize = 26;
        $dateSize = 28;

        // =========================
        // 🧑‍⚕️ NAME
        // =========================
        $nameBoxX = 1380;
        $nameBoxY = 700;
        $nameBoxWidth = 500;
        $nameLineHeight = 35;

        $this->drawCenteredWrappedText(
            $bg1,
            $doctorName,
            $font,
            $nameSize,
            $nameBoxX,
            $nameBoxY,
            $nameBoxWidth,
            $nameLineHeight,
            $blue
        );

        $this->drawCenteredWrappedText(
            $bg2,
            $doctorName,
            $font,
            $nameSize,
            $nameBoxX,
            $nameBoxY,
            $nameBoxWidth,
            $nameLineHeight,
            $blue
        );
        $specBoxX = 1460;
        $specBoxY = 745;
        $specBoxWidth = 310;
        $specSize = 19;
        $lineHeight = 25;

        $this->drawCenteredWrappedText($bg1, $speciality, $fontRegular, $specSize, $specBoxX, $specBoxY, $specBoxWidth, $lineHeight, $lightBlue);
        $this->drawCenteredWrappedText($bg2, $speciality, $fontRegular, $specSize, $specBoxX, $specBoxY, $specBoxWidth, $lineHeight, $lightBlue);

        // =========================
        // 📅 DATE + TIME (ONLY IMAGE 1)
        // =========================
        imagettftext($bg1, 36, 0, 629, 680, $white, $font, $dateText);
        $startTime = strtotime($time);

// +1 hour add
        $endTime = strtotime('+1 hour', $startTime);

// format both
        $startFormatted = date('h:i A', $startTime);
        $endFormatted = date('h:i A', $endTime);

// final text
        $timeRange = $startFormatted . ' To ' . $endFormatted;

// TIME TEXT
        imagettftext($bg1, 34, 0, 629, 800, $white, $font, $timeRange);

        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $doctorName);

        // =========================
        // 💾 TEMP FILES
        // =========================
        $file1 = storage_path('app/banner1.jpg');
        $file2 = storage_path('app/banner2.jpg');

        imagejpeg($bg1, $file1, 100);
        imagejpeg($bg2, $file2, 100);

        // =========================
        // 📦 ZIP CREATE
        // =========================
        $zipPath = storage_path('app/banners.zip');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $zip->addFile($file1, 'Invitation.jpg');
            $zip->addFile($file2, 'Thank_You.jpg');
            $zip->close();
        }

        // cleanup memory
        imagedestroy($bg1);
        imagedestroy($bg2);
        imagedestroy($doctorImg);
        imagedestroy($doctorResized);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function fitTextToWidth($text, $font, $maxWidth, $initialSize)
    {
        $fontSize = $initialSize;

        do {
            $box = imagettfbbox($fontSize, 0, $font, $text);
            $textWidth = $box[2] - $box[0];

            if ($textWidth <= $maxWidth) {
                break;
            }

            $fontSize -= 1;

        } while ($fontSize > 10);

        return $fontSize;
    }
    private function drawCenteredWrappedText($image, $text, $font, $fontSize, $x, $y, $maxWidth, $lineHeight, $color)
    {
        $words = explode(' ', trim($text));
        $lines = [];
        $line = '';

        foreach ($words as $word) {
            $testLine = trim($line . ' ' . $word);
            $box = imagettfbbox($fontSize, 0, $font, $testLine);
            $width = $box[2] - $box[0];

            if ($width <= $maxWidth) {
                $line = $testLine;
            } else {
                if ($line !== '') {
                    $lines[] = $line;
                }
                $line = $word;
            }
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        foreach ($lines as $index => $lineText) {
            $size = $fontSize;

            do {
                $box = imagettfbbox($size, 0, $font, $lineText);
                $lineWidth = $box[2] - $box[0];

                if ($lineWidth <= $maxWidth) {
                    break;
                }

                $size--;
            } while ($size > 10);

            $textX = $x + (($maxWidth - $lineWidth) / 2);
            $textY = $y + ($index * $lineHeight);

            imagettftext($image, $size, 0, (int) $textX, (int) $textY, $color, $font, $lineText);
        }
    }

}
