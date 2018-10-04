<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * JavaHost OpenSID Module - Events Library.
 *
 * @since       0.0.1
 * @author      Rizal Fauzie <rizal@fauzie.my.id>
 * @copyright   PT. Java Digital Nusantara © 2018
 */
class Java_events {

    const DEBUG_NONE = 0;
    const DEBUG_FILTERS = 1;
    const DEBUG_ACTIONS = 2;
    const DEBUG_ALL = 3;

	/**
	 * @var	array	An array of actions
	 */
	protected static $_actions = array();

	/**
	 * @var	array	An array of actions
	 */
	protected static $_filters = array();

	/**
	 * @var	int
	 */
	protected static $_debug_level = self::DEBUG_NONE;

    protected static $_doing_action = false;
    protected static $_doing_filter = false;

    // ------------------------------------------------------------------------

    /**
     * Set debug level for backtrace on log file.
     *
     * @param int $level
     */
    public static function set_debug($level)
    {
        self::$_debug_level = (int)$level;
    }

	// ------------------------------------------------------------------------

	/**
	 * Register
	 *
	 * Registers a Callback for a given event
	 *
	 * @access	public
	 * @param	string	action or filter
	 * @param	string	The name of the event
	 * @param	array	The callback for the Event
	 * @return	void
	 */
	public static function register($type, $event, $callback)
	{
        $key = is_array($callback) ? get_class($callback[0]).'::'.$callback[1] : trim($callback);

        switch ($type) {
            case 'filter':
                self::$_filters[$event][$key] = $callback;
                if (self::$_debug_level == self::DEBUG_FILTERS || self::$_debug_level == self::DEBUG_ALL) {
                    self::log_message('FILTER - Registered filter "'.$key.' with event "'.$event.'"');
                }
                break;
            case 'action':
                self::$_actions[$event][$key] = $callback;
                if (self::$_debug_level == self::DEBUG_ACTIONS || self::$_debug_level == self::DEBUG_ALL) {
                    self::log_message('ACTION - Registered action "'.$key.' with event "'.$event.'"');
                }
                break;
        }
	}

	// ------------------------------------------------------------------------

	/**
	 * Trigger actions.
	 *
	 * Triggers an action event. Return none.
	 *
	 * @access	public
	 * @param	string	The name of the event
	 * @param	mixed	Any data that is to be passed to the action
	 */
	public static function actions($event, $data = '')
	{
        $calls = $data;

		if (self::has_action($event) && self::$_doing_action != $event) {

            self::$_doing_action = $event;

            if (self::$_debug_level == self::DEBUG_ACTIONS || self::$_debug_level == self::DEBUG_ALL) {
                self::log_message('ACION - Triggering action "'.$event.'"');
            }

			foreach (self::$_actions[$event] as $key => $listener) {
                if (self::$_debug_level == self::DEBUG_ACTIONS || self::$_debug_level == self::DEBUG_ALL) {
                    self::log_message('ACION - Running hook "'.$key.'" on event "'.$event.'"');
                }
				if (is_callable($listener)) {
					$calls = call_user_func($listener, $data);
				}
			}

            self::$_doing_action = false;
		}

        return $calls;
	}

    // ------------------------------------------------------------------------

	/**
	 * Trigger filters.
	 *
	 * Triggers an filter event. Return as is.
	 *
	 * @access	public
	 * @param	string	The name of the event
	 * @param	mixed	Any data that is to be passed to the action
	 */
	public static function filters($event, $data = '')
	{
		if (self::has_filter($event) && self::$_doing_filter != $event) {

            self::$_doing_filter = $event;

            if (self::$_debug_level == self::DEBUG_ACTIONS || self::$_debug_level == self::DEBUG_ALL) {
                self::log_message('FILTER - Triggering filter "'.$event.'"');
            }

			foreach (self::$_filters[$event] as $key => $listener) {
                if (self::$_debug_level == self::DEBUG_ACTIONS || self::$_debug_level == self::DEBUG_ALL) {
                    self::log_message('FILTER - Running hook "'.$key.'" on event "'.$event.'"');
                }
				if (is_callable($listener)) {
					$data = call_user_func($listener, $data);
				}
			}

            self::$_doing_filter = false;
		}

        return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Remove Action
	 *
	 * Remove action from on action event.
	 *
	 * @access	public
	 * @param	string	The name of the event
	 * @return	bool	Whether the event has action
	 */
	public static function unset_action($event, $action)
	{
        if (self::$_debug_level == self::DEBUG_ACTIONS || self::$_debug_level == self::DEBUG_ALL) {
            self::log_message('ACTION - Removing action "'.$action.'" from event "'.$event.'".');
        }
        if (isset(self::$_actions[$event]) && isset(self::$_actions[$event][$action])) {
            unset(self::$_actions[$event][$action]);
            return true;
        }
        return false;
	}

	// ------------------------------------------------------------------------

	/**
	 * Remove Filter
	 *
	 * Unsert filter from an event.
	 *
	 * @access	public
	 * @param	string	The name of the event
	 * @return	bool	Whether the event has action
	 */
	public static function unset_filter($event, $filter)
	{
        if (self::$_debug_level == self::DEBUG_FILTERS || self::$_debug_level == self::DEBUG_ALL) {
            self::log_message('ACTION - Removing filter "'.$filter.'" from event "'.$event.'".');
        }
        if (isset(self::$_filters[$event]) && isset(self::$_filters[$event][$filter])) {
            unset(self::$_filters[$event][$filter]);
            return true;
        }
        return false;
	}

	// ------------------------------------------------------------------------

	/**
	 * Has Action
	 *
	 * Checks if the event has action
	 *
	 * @access	public
	 * @param	string	The name of the event
	 * @return	bool	Whether the event has action
	 */
	public static function has_action($event)
	{
        if (self::$_debug_level == self::DEBUG_ACTIONS || self::$_debug_level == self::DEBUG_ALL) {
            self::log_message('ACTION - Checking if event "'.$event.'" has actions.');
        }
        return (isset(self::$_actions[$event]) && count(self::$_actions[$event]) > 0);
	}

    // ------------------------------------------------------------------------

	/**
	 * Has Filter
	 *
	 * Checks if the event has filter
	 *
	 * @access	public
	 * @param	string	The name of the event
	 * @return	bool	Whether the event has filter
	 */
	public static function has_filter($event)
	{
        if (self::$_debug_level == self::DEBUG_FILTERS || self::$_debug_level == self::DEBUG_ALL) {
            self::log_message('FILTER - Checking if event "'.$event.'" has filters.');
        }
        return (isset(self::$_filters[$event]) && count(self::$_filters[$event]) > 0);
	}

	// ------------------------------------------------------------------------

	/**
	 * Log Message
	 *
	 * Pulled out for unit testing
	 *
	 * @param string $type
	 * @param string $message
	 * @return void
	 */
	public static function log_message($message = '')
	{
		if (function_exists('log_message')) {
			log_message('debug', $message);
		}
	}
}

function java_action($event, $object) {
    Java_events::register('action', $event, $object);
}
function java_filter($event, $object) {
    Java_events::register('filter', $event, $object);
}
function java_actions($event, $data = '') {
    return Java_events::actions($event, $data);
}
function java_filters($event, $data = '') {
    return Java_events::filters($event, $data);
}
/* End of file Java_events.php */
