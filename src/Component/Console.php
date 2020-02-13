<?php

namespace PHPMetro\Component;

/**
 * Read from and write to console with a nice API
 * @package PHPMetro
 * @author http://gitlab.com/subiabre
 * @license MIT
 */
class Console
{
    public
        $input,
        $output
        ;

    /**
     * Write to console
     * @param string $message Message to be displayed
     * @return self
     */
    public function write(?string $message): self
    {
        $this->output = $message;

        if (\is_array($message))
        {
            $this->output = $this->arrayToString($message);
        }

        \fwrite(STDERR, \print_r($this->output, TRUE));

        return $this;
    }

    /**
     * Transform an array to an string
     * @param array $message
     * @return string
     */
    public function arrayToString(array $message): string
    {
        $output = "";
        foreach ($message as $key => $value) {
            $output .= $key . ": " . $value . "\n";
        }

        return $output;
    }

    /**
     * Read from console
     * @return string
     */
    public function read(): string
    {
        $handle = \fopen("php://stdin","rb");
        $this->input = \fgets($handle);

        return $this->input;
    }
}
