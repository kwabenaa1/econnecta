<?php

namespace JoomInsights;

new \Joomla\CMS\Helper\LibraryHelper;
/**
 * JoomInsights Client
 *
 * This class is necessary to set project data
 */
class Client
{
    /**
     * The client version
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Hash identifier of the plugin
     *
     * @var string
     */
    public $hash;

    /**
     * Slug of the plugin
     * @example test-slug
     *
     * @var string
     */
    public $slug;

    /**
     * type of the extension
     * @example package
     *
     * @var string
     */
    public $type;

    /**
     * The project version
     *
     * @var string
     */
    public $project_version;

    /**
     * Initialize the class
     *
     * @param string  $hash hash of the plugin
     * @param string  $name readable name of the plugin
     * @param string  $file main plugin file path
     */
    public function __construct($hash, $slug, $type)
    {
        $this->hash = $hash;
        $this->slug = $slug;
        $this->type = $type;

        $this->project_version = '1.0.0';
        
        $this->path = \str_replace(JPATH_ROOT, '', __DIR__);

        // setup system plugin
        // we will introduce it later
        // TODO: use it, its already done
        // $this->setup = $this->setup();
    }

    /**
     * Initialize insights class
     *
     * @return JoomInsights\Insights
     */
    public function setup()
    {
        if (!class_exists(__NAMESPACE__ . '\Setup')) {
            require_once __DIR__ . '/Setup.php';
        }

        $setup = new Setup($this);
        return $setup;
    }

    /**
     * Initialize insights class
     *
     * @return JoomInsights\Insights
     */
    public function insights()
    {
        if (!class_exists(__NAMESPACE__ . '\Insights')) {
            require_once __DIR__ . '/Insights.php';
        }

        return new Insights($this);
    }

    /**
     * API Endpoint
     *
     * @return string
     */
    public function endpoint()
    {
        $endpoint = 'https://joominsights.com/japi.php';

        return rtrim($endpoint, '/\\');
        ;
    }

    /**
     * Submit request to api server
     *
     * @param   string  $url                The URL.
     * @param   string  $routs  additional params
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function send_request($params, $action)
    {
        $version = new \JVersion;
        $httpOption = new \Joomla\Registry\Registry;

        $headers = [
            'user-agent' => 'JoomInsights;',
            'Accept' => 'application/json',
            'Token' => $this->hash,
            'Slug' => $this->slug,
        ];
        
        try {
            $url = $this->endpoint() . '?action=' . $action;
            $http = \JHttpFactory::getHttp($httpOption);

            // limit the request timeout to 2 sec, to avoid server timeout issue
            $response = $http->post($url, [
                'body' => array_merge($params, ['client' => $this->version]),
                'cookies' => [],
                'Token' => $this->hash,
                'slug' => $this->slug,
            ], $headers, 3);

        } catch (\RuntimeException $e) {
            $response = null;
        }

        if ($response === null || $response->code !== 200) {
            // TODO: Add a 'mark bad' setting here somehow
            \JLog::add(\JText::sprintf('JLIB_UPDATER_ERROR_EXTENSION_OPEN_URL', $url), \JLog::WARNING, 'jerror');
            return false;
        }

        return $response;
    }
}
