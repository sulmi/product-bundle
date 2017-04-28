<?php

namespace Sulmi\ProductBundle\Controller;

use FFMpeg\Coordinate\TimeCode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Product base controller.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class ProductBaseController extends Controller
{

    /**
     * It creates thumbnails of the video and adds it to public directory. 
     * Thumbnail is created only once, you can change copies in this method.
     * 
     * @param type $em
     * @param type $videos
     * @return array Array of video entities
     */
    public function getVideoThumbnails($em, $videos)
    {
        $entityNeedUpdate = false;
        foreach ($videos as $videoEntity) {
            /**
             * Extracting image
             * You can extract a frame at any timecode using 
             * the FFMpeg\Media\Video::frame method.
             * This code returns a FFMpeg\Media\Frame instance 
             * corresponding to the second 42. 
             * You can pass any FFMpeg\Coordinate\TimeCode as argument, 
             * see dedicated documentation below for more information.
             */
            if (!$videoEntity->getThumbs()) {
                $entityNeedUpdate = true;
                $basePath = rtrim($this->get('kernel')->getRootDir(), 'app') . 'web/' . $videoEntity->getFilePath();
                $ffmpeg = $this->get('dubture_ffmpeg.ffmpeg');
                $video = $ffmpeg->open($basePath);
                for ($index = 1; $index < 2; $index++) {
                    $fromSeconds = $index * 10;
                    $frame = $video->frame(TimeCode::fromSeconds($fromSeconds));
                    $segment = '-' . $index . '.jpeg';
                    $basePathLoop = $basePath . $segment;
                    $frame->save($basePathLoop);
                    $segment = '';
                    $basePathLoop = '';
                }
                $videoEntity->setThumbs(true);
                $em->persist($videoEntity);
            }
        }
        if ($entityNeedUpdate) {
            $em->flush();
        }
        return $videos;
    }

    /**
     * The basic functionality of sorting collection.
     * 
     * @param Collection $arrayCollection Array unsorted
     * @param string $property Field in entity using magic method
     * @param string $direction asc or desc
     * @return array Array of sorted entities
     */
    public function sortCollection($arrayCollection, $property = 'id', $direction = 'asc')
    {
        $arr = $arrayCollection->getValues();

        if (count($arr) > 0) {
            $arrt = [];

            foreach ($arr as $key => $value) {
                $p = $value->__get($property);
                $arrt[] = $p;
            }

            if (strtolower($direction) == 'asc') {
                natsort($arrt);
                foreach ($arrt as $key => $value) {
                    $arrOut[] = $arr[$key];
                }
            } else {
                $arrt = array_reverse($arrt, true);
                foreach ($arrt as $key => $value) {
                    $arrOut[] = $arr[$key];
                }
            }
            return $arrOut;
        } else {
            return $arr;
        }
    }

}