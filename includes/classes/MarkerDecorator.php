<?php
/**
 * Decorator for current XML labs output.
 * @author mhightower
 *
 */

require_once 'Decorator.php';

class MarkerDecorator extends Decorator {

    protected $childTag;

    public function __construct() {
        $this->childTag = 'marker';
    }

    /**
     * (non-PHPdoc)
     * @see Decorator::getChildTag()
     */
    public function getChildTag() {
        return $this->childTag;
    }

    /**
     * (non-PHPdoc)
     * @see Decorator::show()
     *
     * @assert ('name', 'my movie title') == 'My Movie Title'
     * @assert ('city', 'las vegas') == 'Las Vegas'
     * @assert ('hours', 'saturday 8:00AM') == 'Sat 8:00am'
     */
    public function show($name, $data) {
        switch ($name) {
            case 'name':
            case 'city':
                $rtn = $this->title($data);
            break;
            case 'address':
                $title = $this->title($data);
                $pattern = '/\W+$/';
                $rtn = preg_replace($pattern ,'' ,$title);
            break;
            case 'hours':
                $rtn = $this->dates($data);
            break;
            default:
                $rtn = $data;
            break;
        }
        return $rtn;
    }

    /**
     *
     * Change string to be more title like
     * @param string $str
     *
     * @assert ('my movie title') == 'My Movie Title'
     * @assert ('my movie title') != 'my movie title'
     */
    private function title($str) {
        // return ucwords(... will not work
        $rtn = ucwords(strtolower($str));
        return $rtn;
    }

    private function dates($hours) {
        $hours = str_ireplace("monday", "M", $hours);
        $hours = str_ireplace("mon", "M", $hours);
        $hours = str_ireplace("tuesday", "T", $hours);
        $hours = str_ireplace("tues", "T", $hours);
        $hours = str_ireplace("wednesday", "W", $hours);
        $hours = str_ireplace("wed", "W", $hours);
        $hours = str_ireplace("thursday", "TH", $hours);
        $hours = str_ireplace("thurs", "TH", $hours);
        $hours = str_ireplace("thur", "TH", $hours);
        $hours = str_ireplace("friday", "F", $hours);
        $hours = str_ireplace("fri", "F", $hours);
        $hours = str_ireplace("fr", "F", $hours);
        $hours = str_ireplace("saturday", "Sat", $hours);
        $hours = str_ireplace("sunday", "Sun", $hours);
        $hours = str_ireplace("|", " | ", $hours);
        $hours = str_ireplace("AM", "am", $hours);
        $hours = str_ireplace("PM", "pm", $hours);
        return $hours;
    }
}
