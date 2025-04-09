<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tool;
use App\Models\ToolUnit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ToolController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'tool_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        // Create the new tool
        $tool = Tool::create([
            'name' => $validatedData['tool_name'],
            'description' => $validatedData['description'],
            'quantity' => $validatedData['quantity'],
        ]);

        // Generate QR codes for each tool unit
        for ($i = 0; $i < $tool->quantity; $i++) {
            $serialCode = strtoupper(uniqid("SC-{$tool->name}-", true)); // Generate a unique serial code
            $qrPath = $this->generateQrCode($serialCode, $tool->name); // Generate QR code for the serial code

            // Create the tool unit and associate it with the tool
            ToolUnit::create([
                'tool_id' => $tool->id,
                'serial_code' => $serialCode,
                'qr_path' => $qrPath,
                'status' => 'available', // Default status
                'for_maintenance' => false, // Default maintenance status
            ]);
        }

        return response()->json(['message' => 'Tool created successfully', 'tool' => $tool], 201);
    }

    /**
     * Generate QR Code and store it.
     *
     * @param string $serialCode
     * @return string Path to the QR code image
     */
    // private function generateQrCode($serialCode, $toolName)
    // {
    //     $qrCode = QrCode::format('png')
    //         ->size(200)
    //         ->generate($serialCode);

    //     // Store the QR code in the storage folder and get the path
    //     $path = 'qrcodes/' . $serialCode . '.png';
    //     Storage::disk('public')->put($path, $qrCode);

    //     return $path;
    // }

    private function generateQrCode($serialCode, $toolName)
    {
        // Generate the basic QR code
        $qrCode = QrCode::format('png')
            ->size(200)
            ->margin(3)
            ->generate($serialCode);
        
        // Create an image resource from the QR code
        $qrImage = imagecreatefromstring($qrCode);
        
        // Create a larger canvas to fit QR code and text
        $finalImage = imagecreatetruecolor(240, 240);
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        imagefill($finalImage, 0, 0, $white);
        
        // Copy the QR code to the new canvas
        imagecopy($finalImage, $qrImage, 20, 20, 0, 0, 200, 200);
        
        // Add text to the image
        $textColor = imagecolorallocate($finalImage, 0, 0, 0);
        $font = 5; // Built-in font, or use a custom font with imagettftext
        
        // Center the text
        $textWidth = strlen($toolName) * imagefontwidth($font);
        $x = (240 - $textWidth) / 2;
        
        // Add the text at the bottom
        imagestring($finalImage, $font, $x, 210, $toolName, $textColor);
        
        // Convert image to PNG data
        ob_start();
        imagepng($finalImage);
        $finalQrCode = ob_get_clean();
        imagedestroy($qrImage);
        imagedestroy($finalImage);
        
        // Store the QR code in the storage folder and get the path
        $path = 'qrcodes/' . $serialCode . '.png';
        Storage::disk('public')->put($path, $finalQrCode);
        
        return $path;
    }


    public function index()
    {
        $tools = Tool::with('units')->get();
        return response()->json($tools);
    }

    public function show($id)
    {
        $tool = Tool::with('toolUnits')->find($id);
        return response()->json($tool);
    }

    public function update(Request $request, $id)
    {
        $tool = Tool::find($id);
        $tool->update($request->only([
            'name', 'part_number', 'description', 'quantity', 'location'
        ]));
        return response()->json(['success' => 'Tool updated successfully.'], 200);
    }

    public function destroy($id)
    {
        Tool::find($id)->delete();
        return response()->json(['success' => 'Tool deleted successfully.'], 200);
    }
}