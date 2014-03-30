<?php
abstract class Affiliate {

    protected $type;

    protected $scripts;

    public function getScript() {
        $this->setScript();
        $rtn = '';
        if(is_array($this->scripts)) {
            foreach ($this->scripts as $txt) {
                $rtn .= '<script type="text/javascript">' . PHP_EOL;
                $rtn .= '//<![CDATA[' . PHP_EOL;
                $rtn .= $txt;
                $rtn .= '//]]>' . PHP_EOL;
                $rtn .= '</script>' . PHP_EOL;
            }
        } else {
            $rtn = $txt;
        }
        return $rtn;
    }

    protected function getType() {
        return $type;
    }

    abstract protected function setScript();
}