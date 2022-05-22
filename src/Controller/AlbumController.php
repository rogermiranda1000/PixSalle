<?php


namespace Salle\PixSalle\Controller;

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
    private Twig $twig;
    private AlbumRepository $albumRepository;
    private Messages $flash;

    public function __construct(Twig $twig, AlbumRepository $userRepository, Messages $flash) {
        $this->twig = $twig;
        $this->albumRepository = $userRepository;
        $this->flash = $flash;
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
        $path = 'assets/qrcodes/album_'.$id.'_qr.png';
        file_put_contents($path,$response);
        return $path;
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
        return $this->twig->render(
            $response,
            'album.twig',
            [
                'album' => $albumName,
                'formAction' => '/portfolio/album/'.$album,
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
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            $url = "https://";
        else
            $url = "http://";
        $url.= $_SERVER['HTTP_HOST'];
        $url.= $_SERVER['REQUEST_URI'];

        $this->createQRGuzzle($url, $album);

        return $this->showAlbum($request, $response, $args);
    }
}