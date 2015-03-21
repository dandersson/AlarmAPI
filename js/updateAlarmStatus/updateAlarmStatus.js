'use strict';
/**
 * Create alarm status updater callback using `title_field` as index in the
 * given JavaScript object for the title field, corresponding the API field
 * name for the requested title string.
 *
 * The callback function takes a data structure of the form
 *
 *     {
 *       'state': state,
 *       'time': time,
 *       `title_field`: title
 *     }
 *
 * where
 *
 *   `state` is an int ∈ {1, 0, −1} representing on, off, unknown respectively
 *   `time` is a string describing the time of the last state change
 *   `title` is a readable and localized string describing the state
 *
 * This response data is the set as text content on the DOM elements with IDs
 *
 *   `title` → `alarm-status-text`
 *   `time`  → `alarm-status-time`
 *
 * A FontAwesome [1] icon representing the state is set by applying
 * corresponding classes to the element with ID `alarm-status-icon`.
 *
 * CSS classes are set on the element with `alarm-status-container` corresponding to
 *
 *   Alarm state: on      → alarm-status-container alarm-state-on
 *   Alarm state: off     → alarm-status-container alarm-state-off
 *   Alarm state: unknown → alarm-status-container alarm-state-unknown
 *   Alarm state: error   → alarm-status-container
 *
 * [1]: <http://fortawesome.github.io/Font-Awesome/>
 *
 * @function
 */
var displayAlarmStatus = function displayAlarmStatusClosure() {
  var as_container = document.getElementById('alarm-status-container'),
      as_text = document.getElementById('alarm-status-text'),
      as_time = document.getElementById('alarm-status-time'),
      as_icon = document.getElementById('alarm-status-icon'),
      /** @enum {number} */
      state = {ON: 1, OFF: 0, UNKNOWN: -1},
      as_base_class = 'alarm-status-container',
      fa_base = 'fa fa-fw fa-';

  /**
   * Display a "refresh spinner" icon for a second to visually indicate a
   * status update before setting the state icon.
   *
   * @param {string} icon A FontAwesome icon name designation without the `fa-`
   * prefix.
   */
  function updateFaIcon(icon) {
    as_icon.className = fa_base + 'refresh fa-spin';
    function setFaIcon() { as_icon.className = fa_base + icon; }
    window.setTimeout(setFaIcon, 1500);
  }

  /**
   * Perform actions to display current alarm state and status.
   *
   * `alarm_state` defined as for the callback description in the main function
   * description. A value outside of int{1, 0, −1} will display as an error
   * indicator.
   *
   * @param {number} alarm_state An integer among {1, 0, -1} to denote alarm
   * state.
   * @param {string} time A string describing the time of the status change in
   * question.
   * @param {string} text A string that describes the event.
   */
  return function displayAlarmStatusReturn(alarm_state, time, text) {
    var state_icon,
        state_class;

    switch (alarm_state) {
    case state.ON:
      state_icon = 'lock';
      state_class = 'alarm-state-on';
      break;
    case state.OFF:
      state_icon = 'unlock-alt';
      state_class = 'alarm-state-off';
      break;
    case state.UNKNOWN:
      state_icon = 'question-circle';
      state_class = 'alarm-state-unknown';
      break;
    default:
      state_icon = 'remove';
      state_class = '';
      text = 'Alarm status communication error.';
      time = '';
      break;
    }

    updateFaIcon(state_icon);
    as_container.className = as_base_class + ' ' + state_class;
    as_text.textContent = text;
    as_time.textContent = time;
  }
}();

/**
 * Utility function to initiate a loop to update alarm status indication.
 *
 * @param {string} api_url URL to the API to be called.
 * @param {string} title_field The requested title field from the API.
 * @param {number} interval The interval in ms at which the call shall repeat.
 * @param {function} external_callback An optional callback function which will
 * be run with the alarm status object as parameter every update.
 */
function updateAlarmStatus(api_url, title_field, interval, external_callback) {
  if (typeof external_callback === 'undefined') external_callback = false;
  var request_data = {'fields': [title_field]};

  /**
   * Create the correct callback function to reflect wanted alarm text.
   *
   * @param {string} title_field The requested title field from the API.
   */
  function createCallback(title_field) {
    /**
     * @param {object} as An alarm state API return object.
     */
    function displayCallback(as) {
      displayAlarmStatus(as.state, as.time, as[title_field]);
    }

    if (external_callback) {
      /**
       * @param {object} as An alarm state API return object.
       */
      return function createExtraCallbackReturn(as) {
        displayCallback(as);
        external_callback(as);
      }
    } else {
      /**
       * @param {object} as An alarm state API return object.
       */
      return function createCallbackReturn(as) {
        displayCallback(as);
      }
    }
  }

  /**
   * Perform the initial API request and initiate a self-replicating timer to
   * keep updating the status every `interval` milliseconds.
   */
  (function APIRequestPoller() {
    APIRequest(api_url, request_data, createCallback(title_field));
    window.setTimeout(APIRequestPoller, interval);
  })();
};

/** Function exports for Closure Compiler. */
window['updateAlarmStatus'] = updateAlarmStatus;
