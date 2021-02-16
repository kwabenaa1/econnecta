<?php

namespace JoomInsights;

/**
 * JoomInsights Insights
 *
 * This is a tracker class to track plugin usage based on if the customer has opted in.
 * No personal information is being tracked by this class, only general settings, active plugins, environment details
 * and admin email.
 */
class Insights
{
    /**
     * JoomInsights\Client
     *
     * @var object
     */
    protected $client;

    /**
     * extra_data
     *
     * @var object
     */
    protected $extra_data;

    /**
     * Initialize the class
     *
     * @param JoomInsights\Client
     */
    public function __construct($client, $slug = null)
    {
        if (is_string($client) && !empty($slug)) {
            $client = new Client($client, $slug);
        }

        if (is_object($client) && is_a($client, 'JoomInsights\Client')) {
            $this->client = $client;
        }
    }

    /**
     * Initialize insights
     *
     * @return void
     */
    public function init($askPermission = false)
    {
        if($askPermission){
            $dispatcher = \JDispatcher::getInstance();
            $dispatcher->trigger('onJoomInsightsAfterInstall', array($this));
        }
    }

    /**
     * Send tracking data to JoomInsights server
     *
     * @param  boolean  $override
     *
     * @return void
     */
    public function send_tracking_data($type = 'install')
    {
        if (!$this->tracking_allowed()) {
            return;
        }

        // Send a maximum of once per week
        $last_send = $this->get_last_send();
        if ($last_send && $last_send > strtotime('-1 week')) {
            return;
        }

        $this->client->send_request($this->get_tracking_data(), $type);
        return true;
    }

    /**
     * Get the tracking data points
     *
     * @return array
     */
    public function get_tracking_data()
    {
        $app = \JFactory::getApplication();
        $db = \JFactory::getDBo();
        $name = $app->getCfg('fromname');
        $email = $app->getCfg('mailfrom');
        $version = new \JVersion;

        $langs = \JLanguageHelper::getLanguages('lang_code');
        $isMultilang = count($langs) > 1;

        $allLangs = array_map(function ($item) {return $item->lang_code;}, $langs);

        $data = [
            'url' => \JUri::root(),
            'site' => $app->getCfg('sitename'),
            'admin_email' => $email,
            'first_name' => $name,
            'server' => $this->get_server_info(),
            'users' => $this->get_user_counts(),
            'extensions' => $this->get_all_extensions(),
            'ip_address' => $this->get_user_ip_address(),
            'template' => $this->get_default_template(),
            'jversion' => $version->getShortVersion(),
            'databasetype' => $db->name,
            'dbversion' => $db->getVersion(),
            'locales' => $allLangs,
            'multilingual' => $isMultilang
        ];

        // Add metadata
        if ($extra = $this->get_extra_data()) {
            $data['extra'] = $extra;
        }

        return $data;
    }

    /**
     * Add extra data if needed
     *
     * @param array $data
     *
     * @return \self
     */
    public function add_extra($data = [])
    {
        $this->extra_data = $data;

        return $this;
    }

    /**
     * If a child class wants to send extra data
     *
     * @return mixed
     */
    protected function get_extra_data()
    {
        $extra_data = $this->extra_data;

        if (is_callable($extra_data)) {
            return $extra_data();
        } elseif (is_array($extra_data)) {
            return $extra_data;
        }

        return [];
    }

    /**
     * Check if the user has opted into tracking
     * TODO: store config for tracking disabled
     * @return bool
     */
    private function tracking_allowed()
    {
        $allow_tracking = 'yes';
        return $allow_tracking == 'yes';
    }

    /**
     * Get the last time a tracking was sent
     *
     * @return false|string
     */
    private function get_last_send()
    {
        return false;
        // return get_option($this->client->slug . '_tracking_last_send', false);
    }

    /**
     * Check if the current server is localhost
     *
     * @return boolean
     */
    private function is_local_server()
    {
        $is_local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

        return $is_local;
    }

    /**
     * Get server related info.
     *
     * @return array
     */
    private static function get_server_info()
    {
        $server_data = [];

        if (isset($_SERVER['SERVER_SOFTWARE']) && !empty($_SERVER['SERVER_SOFTWARE'])) {
            $server_data['software'] = $_SERVER['SERVER_SOFTWARE'];
        }

        if (function_exists('phpversion')) {
            $server_data['php_version'] = phpversion();
        }

        return $server_data;
    }

    /**
     * Get user totals based on user role.
     *
     * @return array
     */
    public function get_user_counts()
    {
        // query count users
        $input = \JFactory::getApplication()->input;
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(*) as total')->from('#__users');
        $db->setQuery($query);
        return $db->loadObject()->total;
    }

    /**
     * Get user totals based on user role.
     *
     * @return array
     */
    public function get_default_template()
    {
        // query count users
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__template_styles')->where('client_id = 0')->where('home = 1');
        $db->setQuery($query);
        return $db->loadObject()->template;
    }

    /**
     * Get user totals based on user role.
     *
     * @return array
     */
    public function get_all_extensions()
    {
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__extensions');
        $db->setQuery($query);
        $getAllExtensions = $db->loadObjectList();
        $newList = [];

        $cores = \JExtensionHelper::getCoreExtensions();
        $coreComp = array_map(function ($item) { return $item[1]; }, $cores);
        $coreComp = array_unique($coreComp);

        foreach ($getAllExtensions as $key => $extension) {
            $response = [];
            if (in_array($extension->element, $coreComp)) {
                continue;
            } else {
                $newList[] = $extension->element;
            }
        }
        $newList = array_unique($newList);
        return $newList;
    }

    /**
     * Get user IP Address
     */
    private function get_user_ip_address()
    {
        $process = true;
        // Get the handler to download the blocks
        $httpOption = new \Joomla\Registry\Registry;
        $http = \JHttpFactory::getHttp($httpOption);
        
        try {
            // request timeout limit to 2 sec, so we dont dead the server timeout
            $result = $http->get('https://icanhazip.com/', null, 2);
            if ($result->code != 200 && $result->code != 310) {
                return '';
            }
            $ip = trim($result->body);

            return $ip;
        } catch (\Throwable $th) {
            return '0.0.0.0';
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return '';
        }

        return $ip;
    }
}
