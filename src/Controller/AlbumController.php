<?php


namespace Salle\PixSalle\Controller;

use FilesystemIterator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\ImageRepository;
use Salle\PixSalle\Repository\AlbumRepository;
use Salle\PixSalle\Service\ValidatorService;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class AlbumController
{
    const __QR_PATH__ = 'assets/qrcodes/';
    private Twig $twig;
    private AlbumRepository $albumRepository;
    private Messages $flash;

    public function __construct(Twig $twig, AlbumRepository $userRepository, Messages $flash) {
        $this->twig = $twig;
        $this->albumRepository = $userRepository;
        $this->flash = $flash;
    }

    private function getQrPath($album) {
        return AlbumController::__QR_PATH__.'album_qr_'.$album.'.png';
    }

    private function createQRGuzzle($url, $id)
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->request('POST', 'http://barcode/barcodegenerator',
            ['headers' =>
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'image/png',
                ],
                'json' =>
                    [
                        'symbology' => 'QRCode',
                        'code' => $url
                    ]
                ]
        );
        $response = $request->getBody();
        $path = $this->getQrPath($id);
        file_put_contents($path,$response);
        return '/'.$path;
    }

    private function qrExists($album) {
        return file_exists($this->getQrPath($album));
    }

    public function showAlbum(Request $request, Response $response, array $args): Response
    {
        $album = intval($args['id']);
        $photos = $this->albumRepository->getAlbumPhotos($album);
        $albumName = $this->albumRepository->getAlbumName($album);
        $disable = false;
        if ($albumName == '') {
            $albumName = "This album does not exist";
            $disable = true;
        }

        $qr = null;
        if (array_key_exists('qr', $args)) $qr = $args['qr'];
        if ($this->qrExists($album)) {
            $qr = '/'.$this->getQrPath($album);
        }
        return $this->twig->render(
            $response,
            'album.twig',
            [
                'qr' => $qr,
                'album' => $albumName,
                'formAction' => '/portfolio/album/'.$album,
                'formActionQr' => '/portfolio/album/qr/'.$album,
                'photos' => $photos,
                'disable' => $disable
            ]
        );
    }

    public function deleteAlbum(Request $request, Response $response, array $args): Response
    {
        $album = intval($args['id']);
        $data = $request->getParsedBody();
        if ($data['id'] == null) {
            $this->albumRepository->deleteAlbum($album);
        } else {
            $photo_id = $data['id'];
            $this->albumRepository->deletePhoto($album, $photo_id);
        }
        return $this->showAlbum($request, $response, $args);
    }

    public function uploadPhoto(Request $request, Response $response, array $args): Response
    {
        $album = intval($args['id']);
        $url = $request->getParsedBody()['imageUrl'];

        $this->albumRepository->addPhoto($album, $url);
        return $this->showAlbum($request, $response, $args);
    }

    public function createQr(Request $request, Response $response, array $args): Response
    {
        $album = intval($args['id']);
        if (!$this->qrExists($album)) {
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            $url.= $_SERVER['HTTP_HOST'];
            $url.= $_SERVER['REQUEST_URI'];

            $args['qr'] = $this->createQRGuzzle($url, $album);
        }
        $this->makeSpaceQr();

        return $this->showAlbum($request, $response, $args);
    }

    // Leave maximum one QR
    private function makeSpaceQr() {
        $files = glob(AlbumController::__QR_PATH__.'*.png');

        if (count($files) > 2) {
            $oldest = PHP_INT_MIN;
            $toDelete = null;
            foreach ($files as $file) {
                $age = time() - filectime($file);
                if ($age > $oldest) {
                    $toDelete = $file;
                    $oldest = $age;
                }
            }
            if ($toDelete != null) {
                unlink($toDelete);
            }
        }
    }

    public function downloadQr(Request $request, Response $response, array $args): Response
    {
        $album = intval($args['id']);
        $filePath = $this->getQrPath($album);
        if ($this->qrExists($album) && file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            flush(); // Flush system output buffer
            readfile($filePath);
        }
        return $this->showAlbum($request, $response, $args);
    }
}