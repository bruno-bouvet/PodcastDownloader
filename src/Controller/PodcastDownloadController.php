<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class PodcastDownloadController extends AbstractController
{
    /**
     * @Route("/", name="podcast")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $url = null;

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('name', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'choices' => [
                    'Faskill' => [
                        'Morceaux Choisies' => 'MC',
                        'Chill Pill' => 'ChillPill',
                    ],
                    'Beja' => [
                        'RDV Tech' => 'RDVTECH',
                        'RDV Jeux' => 'RDVJEUX',
                    ],
                ], 'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('limite', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'choices' => [
                    'Options' => [
                        'Last Episode' => 1,
                        'Last 3 episodes' => 3,
                        'all' => 50
                    ],
                ], 'constraints' => [
                    new NotBlank()
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data = json_decode(json_encode($data), FALSE);
            $name = $data->name;
            $limite = $data->limite;

            if ($name !== null) {
                switch ($name):
                    case 'MC':
                        $url = 'https://mc.faskil.com/feed/podcast/';
                        break;
                    case 'ChillPill':
                        $url = 'https://chillpill.faskil.com/feed/podcast/';
                        break;
                    case 'RDVTECH':
                        $url = 'http://feeds.feedburner.com/lerendezvoustech';
                        break;
                    case 'RDVJEUX':
                        $url = 'http://feeds.feedburner.com/lerendezvousjeux';
                        break;
                endswitch;
            }

            $myDir = 'podcasts/xml';
            $this->createFolder($myDir);

            $Dir = $myDir . '/flux.xml';
            $this->download($url, $Dir);

            // read the file and download
            $feeds = null;

            $invalidurl = false;
            if (@simplexml_load_string(file_get_contents($url))) {
                $feeds = simplexml_load_string(file_get_contents($url));
            } else {
                $invalidurl = true;
                echo '<h2>Invalid RSS feed URL.</h2>';
            }
            $i = 0;
            if ($feeds !== null) {
                $site = $feeds->channel->title;
                //echo '<h1>' . $site . '</h1>';
                foreach ($feeds->channel->item as $item) {
                    if ($item->title !== null) {
                        $podcastTitle = $item->title;
                    }
                    if ($item->enclosure->attributes()->{'url'} !== null) {
                        $url = $item->enclosure->attributes()->{'url'};

                        switch ($url) :
                            case strpos($url, 'media.blubrry.com/faskil_chillpill') !== false :
                                $url = str_replace('media.blubrry.com/faskil_chillpill/', '', $url);
                                break;
                            case strpos($url, 'media.blubrry.com/morceaux') !== false :
                                $url = str_replace('media.blubrry.com/morceaux/', '', $url);
                                break;
                            case strpos($url, 'http://feedproxy.google.com/~r/lerendezvoustech') !== false :
                                $explodeUrl = explode('/', $url);
                                $podcastName = end($explodeUrl);
                                $redirect = 'http://frenchspin.com/sites/lrdv/audio/';
                                $url = $redirect . $podcastName;
                                break;
                            case strpos($url, 'http://www.podtrac.com/pts/redirect.mp3/frenchspin.com/sites/rdvjeux/audio/') !== false :
                                $explodeUrl = explode('/', $url);
                                $podcastName = end($explodeUrl);
                                $redirect = 'http://frenchspin.com/sites/rdvjeux/audio/';
                                $url = $redirect . $podcastName;
                                break;
                        endswitch;

                        $myDir = 'podcasts/' . $name;
                        $this->createFolder($myDir);

                        $Dir = $myDir . '/' . $podcastTitle . '.mp3';
                        $this->download($url, $Dir);

                        $i++;
                        if ($i === $limite) {
                            break;
                        }
                        if ($i === 5 && $i % 2) {
                            sleep(20);
                        }
                    }
                }
                $this->addFlash('success', "You downloaded $i episodes of " . '"'. $site . '"');
            }

            return $this->render('pages/podcast.html.twig', [
                'form' => $form->createView(),
                'name' => $name
            ]);
        }
        return $this->render('pages/podcast.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param $myDir string
     */
    public function createFolder($myDir)
    {
        if (!is_dir($myDir) && !mkdir($myDir, 0777, true) && !is_dir($myDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $myDir));
        }
    }

    /**
     * @param string $url
     * @param string $Dir
     */
    public function Download(string $url, string $Dir)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //Create a new file where you want to save
        $fp = fopen($Dir, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }
}
