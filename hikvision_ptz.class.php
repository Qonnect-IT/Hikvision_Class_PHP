<?php

  Class Hikvision_PTZ {

    var $host = '';
    var $user = '';
    var $pass = '';
    var $transport = '';

    var $result = false;

    /**
    * @param string $host
    * @param string $user
    * @param string $pass
    * @param string $transport
    */
    public function __construct($cam = null, $host = null, $user = null, $pass = null, $transport = "https")
    {

        if($host == null && $user == null && $pass == null)
        {
            include("config.inc.php");
        }

	$this->cam = $cam;
        $this->host = $cameras[$this->cam]["host"];
        $this->user = $cameras[$this->cam]["user"];
        $this->pass = $cameras[$this->cam]["pass"];
        $this->transport = $cameras[$this->cam]["transport"];
    }

    /**
    *
    */
    public function return_cam()
    {
      return $this->cam;
    }

    /**
    * @param string $pan
    * @param string $tilt
    * @param string $zoom
    */
    public function control_relative($pan = null, $tilt = null, $zoom = null)
    {
      $url = "/ISAPI/PTZCtrl/channels/1/Momentary";

      $xml = '<PTZData>
              <pan>' . $pan . '</pan>
              <tilt>' . $tilt . '</tilt>
              <zoom>' . $zoom . '</zoom>
              <Momentary>
                <duration>500</duration>
              </Momentary>
            </PTZData>';

      syslog(LOG_INFO, "CamControl: Sending momentary commands to " . $this->cam);
      
      $this->curl_put_data($url, $xml);
 
    }

    /**
    * @param string $azimut
    * @param string $elevation
    * @param string $zoom
    */
    public function control_absolute($azimuth = null, $elevation = null, $zoom = null)
    {
      $url = "/ISAPI/PTZCtrl/channels/1/Absolute";

      $xml = '<PTZData>
              <AbsoluteHigh>
                <azimuth>' . $azimuth . '</azimuth>
                <elevation>' . $elevation . '</elevation>
                <absoluteZoom>' . $zoom . '</absoluteZoom>
              </AbsoluteHigh>
            </PTZData>';

      syslog(LOG_INFO, "CamControl: Sending absolute commands to " . $this->cam);

      $this->curl_put_data($url, $xml);
    }


    /**
    * @param string $urlpath
    * @param array $putdata
    */
    public function curl_put_data($urlpath, $putdata)
    {

        $url = $this->transport . "://" . $this->user . ":" . $this->pass . "@" . $this->host . $urlpath;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array('Content-Type: application/xml'),
            CURLOPT_CUSTOMREQUEST => 'PUT',
	    CURLOPT_POSTFIELDS => $putdata,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_COOKIE => "",
            CURLOPT_SSL_VERIFYHOST => 0,
	    CURLOPT_SSL_VERIFYPEER => 0,
	    CURLOPT_VERBOSE => 1
        ));

        $result = curl_exec($curl);

        return $result;
    }


  }

?>
