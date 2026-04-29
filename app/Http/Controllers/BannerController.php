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

        // =========================
        // 🔠 FONT SIZES
        // =========================
        $maxNameWidth = 400;

        $nameSize = $this->fitTextToWidth($doctorName, $font, $maxNameWidth, 32);

        $specSize = 26;
        $dateSize = 28;

        // =========================
        // 🧑‍⚕️ NAME
        // =========================
        imagettftext(
            $bg1,
            $nameSize,
            0,
            1450,
            680,
            $blue,
            $font,
            $doctorName
        );

// NAME IMAGE 2
        imagettftext(
            $bg2,
            $nameSize,
            0,
            1450,
            680,
            $blue,
            $font,
            $doctorName
        );
        $maxSpecWidth = 420;
        $specSize = $this->fitTextToWidth($speciality, $font, $maxSpecWidth, 30);

        // =========================
        // 🩺 SPECIALITY
        // =========================
        imagettftext($bg1, $specSize, 0, 1450, 750, $lightBlue, $font, $speciality);
        imagettftext($bg2, $specSize, 0, 1450, 750, $lightBlue, $font, $speciality);

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
}
