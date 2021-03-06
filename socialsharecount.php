<?php

/**
 * Class SocialShareCount
 *
 * Class to count share in facebook, twitter, linkedin, google plus, pinterest, delicious
 */
class SocialShareCount {
    /**
     * Site/page URL
     *
     * @var string
     */
    private $url;

    /**
     * Constructor
     *
     * @param       $url        string          site url
     */
    public function __construct($url) {
        $this->url=rawurlencode($url);
    }

    /**
     * Get facebook share count
     *
     * @access      public
     * @return      int         Total count
     */
    public function getFacebookCount() {
        $data = $this->processCurl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$this->url);
        $response = json_decode($data, true);

        $data = array();

        $data['share_count'] = isset($response[0]['share_count'])?intval($response[0]['share_count']):0;
        $data['like_count'] = isset($response[0]['like_count'])?intval($response[0]['like_count']):0;
        $data['comment_count'] = isset($response[0]['comment_count'])?intval($response[0]['comment_count']):0;
        $data['total_count'] = isset($response[0]['total_count'])?intval($response[0]['total_count']):0;

        return $data;
    }

    /**
     * Get tweet count
     *
     * @access      public
     * @return      int         Total number of tweets
     */
    function getTweetCount() {
        $data = $this->processCurl('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->url);
        $response = json_decode($data, true);
        return isset($response['count'])?intval($response['count']):0;
    }

    /**
     * Get linkedin share count
     *
     * @access      public
     * @return      int          Total number of share in linkedin
     */
    function getLinkedinShareCount() {
        $data = $this->processCurl("http://www.linkedin.com/countserv/count/share?url=$this->url&format=json");
        $response = json_decode($data, true);
        return isset($response['count'])?intval($response['count']):0;
    }

    /**
     * Get google plus count
     *
     * @access      public
     * @return      int         Total number of share in google plus
     */
    public function getGooglePlusCount()  {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($this->url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        $curl_results = curl_exec ($curl);
        curl_close ($curl);
        $json = json_decode($curl_results, true);
        return isset($json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
    }

    /**
     * Get pin count from pinterest
     *
     * @access      public
     * @return      int         Total pin count
     */
    public function getPinterestShareCount() {
        $return_data = $this->processCurl('http://api.pinterest.com/v1/urls/count.json?url='.$this->url);
        $json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
        $json = json_decode($json_string, true);
        return isset($json['count'])?intval($json['count']):0;
    }

    /**
     * Get share count from delicious
     *
     * @access      public
     * @return      int         Total count from delicious
     */
    public function getDeliciousShare() {
        $json_string = $this->processCurl('http://feeds.delicious.com/v2/json/urlinfo/data?url='.$this->url);
        $json = json_decode($json_string, true);
        return isset($json[0]['total_posts'])?intval($json[0]['total_posts']):0;
    }

    /**
     * Process request using curl
     *
     * @access      private
     * @param       $url        string      Url to process
     * @return      mixed                   Curl processing result
     */
    private function processCurl($url){
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $cont = curl_exec($ch);
        if(curl_error($ch))
        {
            die(curl_error($ch));
        }
        return $cont;
    }
}
?>