<?php

namespace Furiganafy;

class Furiganafy{
    const ENC = 'utf-8';
    private $input;
    private $length;
    private $result;
    private $base;
    private $top;

    /**
     * Does the magic. Goes through the input string by singular chars.
     * If its a bracket the following sequence gets saved.
     *
     * Brackets inside of brackets are ignored.
     */
    public function main(){
        $this->setInput($this->input());
        $this->setLength($this->length());
        $this->setResult('');
        $found = false;

        $this->setBase('');
        $this->setTop('');

        for($i = 0; $i <= $this->getLength(); $i++){
            $current = $this->getChar($i);

            if(!$found){
                if($current == '{'){
                    $pos = $this->findCharPos('}', $i);
                    $specialLength = $pos-$i;
                    $this->setBase($this->extract($i, $specialLength));

                    $i += $specialLength;
                    $found = true;
                } else {
                    $this->addResult($current);
                }
            } else {
                if($current == '['){
                    $pos = $this->findCharPos(']', $i);
                    $specialLength = $pos-$i;
                    $this->setTop($this->extract($i, $specialLength));
                    $i += $specialLength;
                    $found = false;

                    $this->addResult($this->rubify());
                    $this->setBase('');
                    $this->setTop('');
                }
            }
        }

        $this->cleanUp();
        $this->display();
    }

    /**
     * Helper function to get the current input from the textarea.
     *
     * @return string
     */
    private function input(){
        return isset($_POST['text']) ? $_POST['text'] : "";
    }

    /**
     * Calculates the string length multi-byte safe with set encoding.
     *
     * @return int
     */
    private function length(){
        return mb_strlen($this->getInput(), Furiganafy::ENC);
    }

    /**
     * Returns the requested char of the input. Multi-byte safe.
     *
     * @param $pos
     * @return string
     */
    private function getChar($pos){
        return mb_substr($this->getInput(), $pos, 1, Furiganafy::ENC);
    }

    /**
     * Finds the requested char in the input string. Used for '}' and ']'. Multi-byte safe.
     *
     * @param $char
     * @param int $offset
     * @return false|int
     */
    private function findCharPos($char, $offset = 0){
        return mb_strpos($this->getInput(), $char, $offset, Furiganafy::ENC);
    }

    /**
     * Extracts the char sequence from input for given length and starting values. Multi-byte safe.
     *
     * @param $start
     * @param $length
     * @return string
     */
    private function extract($start, $length){
        return mb_substr($this->getInput(), $start, $length, Furiganafy::ENC);
    }

    /**
     * Deletes all remaining '{' and '[' from the result string.
     */
    private function cleanUp(){
        $result = str_replace('{', '', $this->getResult());
        $result = str_replace('[', '', $result);
        $this->setResult($result);
    }

    /**
     * Converts base and top to ruby string (HTML).
     *
     * @return string
     */
    private function rubify(){
        $base = $this->getBase();
        $top = $this->getTop();

        $html = <<<EOT
<ruby>
  <rb>$base</rb>
  <rt>$top</rt>
</ruby>

EOT;
        return $html;
    }

    /**
     * Displays the html.
     */
    private function display(){
        $text = $this->getResult();
        $html = <<<EOT
        <html>
            <head>
                <title>Furiganafy</title>
            </head>
            <body>
                <p>$text</p>
                <form method="post" action="">
                    <textarea name="text" rows="20" cols="100">$text</textarea>
                    <br />
                    <input type="submit" value="Convert">
                </form>
            </body>
        </html>
EOT;

        echo $html;
    }

    /***  GETTER & SETTER  ***/
    public function getInput(){
        return $this->input;
    }
    public function setInput($input){
        $this->input = $input;
    }
    public function getLength(){
        return $this->length;
    }
    public function setLength($input){
        $this->length = $input;
    }
    public function getResult(){
        return $this->result;
    }
    public function setResult($input){
        $this->result = $input;
    }
    public function addResult($input){
        $this->setResult($this->getResult().$input);
    }
    public function getBase(){
        return $this->base;
    }
    public function setBase($input){
        $this->base = $input;
    }
    public function getTop(){
        return $this->top;
    }
    public function setTop($input){
        $this->top = $input;
    }
}

//Run the app
(new Furiganafy())->main();