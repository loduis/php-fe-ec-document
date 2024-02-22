<?php

namespace FEEC\Document;

use Picqer\Barcode\Types\TypeCode128;

class Barcode
{
    private string $code;

    private TypeCode128 $generator;

    private int $widthFactor;

    private int $height;

    private array $foregroundColor;

    public function __construct(string $code, int $height = 30, int $widthFactor = 2, array $foregroundColor = [0, 0, 0])
    {
        $this->code = $code;
        $this->generator = new TypeCode128();
        $this->widthFactor = $widthFactor;
        $this->height = $height;
        $this->foregroundColor = $foregroundColor;
    }

    public function __toString()
    {
        $barcodeData = $this->generator->getBarcodeData($this->code);
        $width = round($barcodeData->getWidth() * $this->widthFactor);

        $image = imagecreate($width, $this->height);
        $colorBackground = imagecolorallocate($image, 255, 255, 255);
        imagecolortransparent($image, $colorBackground);

        $gdForegroundColor = imagecolorallocate($image,
            ...$this->foregroundColor
        );
        $height = $this->height;
        $widthFactor = $this->widthFactor;
        $positionHorizontal = 0;
        foreach ($barcodeData->getBars() as $bar) {
            $barWidth = round(($bar->getWidth() * $widthFactor), 3);
            if ($bar->isBar() && $barWidth > 0) {
                $y = round(($bar->getPositionVertical() * $height / $barcodeData->getHeight()), 3);
                $barHeight = round(($bar->getHeight() * $height / $barcodeData->getHeight()), 3);
                imagefilledrectangle($image, $positionHorizontal, $y, ($positionHorizontal + $barWidth - 1), ($y + $barHeight), $gdForegroundColor);
            }
            $positionHorizontal += $barWidth;
        }

        ob_start();
        imagepng($image);
        $content = ob_get_clean();
        imagedestroy($image);
        return $content;
    }

    public function save(string $filename)
    {
        file_put_contents($filename, (string) $this);
    }

    public function toBase64()
    {
        return base64_encode((string) $this);
    }
}
