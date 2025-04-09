<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class BorrowerController extends Controller
{
    public function storeBorrower(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8',
        ]);

        $borrower = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => bcrypt($validatedData['password']),
            'role' => 'borrower',
            'serial_code' => Str::uuid(),
        ]);

        $qrCodeData = $borrower->serial_code;

        // Generate QR code image
        $qrCode = QrCode::format('png')
            ->size(200)
            ->margin(3)
            ->generate($qrCodeData);

        // Create image from QR code
        $qrImage = imagecreatefromstring($qrCode);

        // Create final image with extra space for text
        $finalImage = imagecreatetruecolor(240, 240);
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        imagefill($finalImage, 0, 0, $white);

        // Copy QR image onto final canvas
        imagecopy($finalImage, $qrImage, 20, 20, 0, 0, 200, 200);

        // Add borrower's name as text at the bottom
        $textColor = imagecolorallocate($finalImage, 0, 0, 0);
        $font = 3; // Adjust if you want a different size
        $text = $borrower->name;
        $textWidth = strlen($text) * imagefontwidth($font);
        $x = (240 - $textWidth) / 2;
        imagestring($finalImage, $font, $x, 225, $text, $textColor);

        // Capture the image output
        ob_start();
        imagepng($finalImage);
        $finalQrCode = ob_get_clean();

        imagedestroy($qrImage);
        imagedestroy($finalImage);

        // Store in public disk
        $qrCodePath = "qrcodes-borrowers/{$borrower->id}.png";
        Storage::disk('public')->put($qrCodePath, $finalQrCode);

        // Save path in DB
        $borrower->qr_path = $qrCodePath;
        $borrower->save();

        return response()->json([
            'message' => 'Borrower created successfully',
            'borrower' => $borrower,
            'qr_code_url' => Storage::url($qrCodePath)
        ], 201);
    }


    public function indexBorrower()
    {
        $borrowers = User::where('role', 'borrower')->get();
        return response()->json($borrowers);
    }

    public function showBorrower($id)
    {
        $borrower = User::findOrFail($id);
        return response()->json($borrower);
    }

    public function updateBorrower(Request $request, $id)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|string|max:15',
        ]);

        // Update the borrower
        $borrower = User::findOrFail($id);
        $borrower->update($validatedData);

        return response()->json(['message' => 'Borrower updated successfully', 'borrower' => $borrower]);
    }

    public function destroyBorrower($id)
    {
        $borrower = User::findOrFail($id);
        $borrower->delete();

        return response()->json(['message' => 'Borrower deleted successfully']);
    }
}
