<?php
class ImageStamps {
    /**
     * generate the image url to the image
     * @param array $image the image data array
     * @return string the image url string
     * @note if image doesn't have a mac_address it will use hub.
     */
    public static function ImageURL($image) {
        if(is_null($image['mac_address'])){
            $server = Servers::GetHub();
        } else {
            $server = Servers::ServerMacAddress($image['mac_address']);
        }
        return "http:://".$server['url'].$image['path'].$image['file'];
    }
    /**
     * get the user images with urls
     * @param int $user_id the user id
     * @return array a list of image data arrays with urls
     */
    public static function UserImages($user_id){
        $images = ImageFile::UserImages($user_id);
        for($i = 0; $i < count($images); $i++){
            $images[$i]['url'] = ImageStamps::ImageURL($images[$i]);
        }
        return $images;
    }
    /**
     * get a list of images with all tags ImageHasAllTags("profile","icon")
     */
    public static function ImageHasAllTags(){
        $tags = func_get_args();
        return ImageStamps::ImageTags($tags,false);
    }
    /**
     * get a list of images with any of the tags ImageHasAnyTags("banner","icon")
     */
    public static function ImageHasAnyTags(){
        $tags = func_get_args();
        return ImageStamps::ImageTags($tags,true);
    }
    /**
     * get a list of images with any of the tags ImageTags(["banner","icon"],true)
     */
    public static function ImageTags($tags,$any){
        $tags = func_get_args();
        $image_refs = [];
        // load and count all images
        foreach($tags as $tag){
            $tagged_images = ImageTags::TaggedImages($tag);
            foreach($tagged_images as $tagged_image){
                if(isset($image_refs[$tagged_image['guid']])){
                    $image_refs[$tagged_image['guid']]++;
                } else {
                    $image_refs[$tagged_image['guid']] = 0;
                }
            }
        }
        $images = [];
        foreach($image_refs as $guid => $count){
            if($any || $count == count($tags)){
                $image= ImageFile::ImageGUID($guid);
                $image['url'] = ImageStamps::ImageURL($image);
                $images[] = $image;
            }
        }
        return $images;
    }
}
/**
 * a util function that looks at a folder and returns a list of images without the extensions
 * @param string $folder the folder to look in
 * @return array a list of image names without the extensions
 * @note this is used to get a list of images in a folder
 */
function ImageFolderToList($folder){
    $images = [];
    $files = scandir($folder);
    foreach($files as $file){
        if($file != "." && $file != ".."){
            $images[] = substr($file,0,strrpos($file,"."));
        }
    }
    return $images;
}
?>