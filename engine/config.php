<?php

namespace Engine;

/**
 * Config
 *
 * @author François LASSERRE
 * @copyright Copyright (c) 2016 All rights reserved.
 */
class Config
{
    private $config_music = [];
    private $config_playlist = [];
    private $config_stations = [];

    /**
     * __construct
     *
     * @param bool $music_ext
     * @param mixed $music_path
     * @param mixed $playlist_path
     * @param mixed $playlist_size
     * @param array $stations
     * @access public
     * @return void
     */
    public function __construct($music_ext = array('mp3'), $music_path, $playlist_path, $playlist_size, $stations)
    {
        $this->config_music['ext'] = $music_ext;
        $this->config_music['path'] = $music_path;
        $this->config_playlist['path'] = $playlist_path;
        $this->config_playlist['size'] = $playlist_size;

        foreach ($stations as $station_name => $rules) {
            $this->addStation(new Station($station_name, $rules));
        }

        $this->cr = "\n";

        if (DIRECTORY_SEPARATOR == "\\") {
            $this->cr = "\r\n";
        }
    }

    /**
     * addStation
     *
     * @param Station $station
     * @access public
     * @return void
     */
    public function addStation(Station $station)
    {
        $this->config_stations[] = $station;
    }

    /**
     * getMusicExt
     *
     * @access public
     * @return array
     */
    public function getMusicExt()
    {
        return $this->config_music['ext'];
    }

    /**
     * getMusicFolder
     *
     * @access public
     * @return string
     */
    public function getMusicFolder()
    {
        return $this->config_music['path'];
    }

    /**
     * getPlaylistPath
     *
     * @access public
     * @return string
     */
    public function getPlaylistPath()
    {
        return $this->config_playlist['path'];
    }

    /**
     * getPlaylistSize
     *
     * @access public
     * @return int
     */
    public function getPlaylistSize()
    {
        return $this->config_playlist['size'];
    }

    /**
     * getStations
     *
     * @access public
     * @return array
     */
    public function getStations()
    {
        return $this->config_stations;
    }

    /**
     * getCr
     *
     * @access public
     * @return string
     */
    public function getCr()
    {
        return $this->cr;
    }
}
