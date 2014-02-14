<?php
// CSV Iterator
// see: http://uk1.php.net/fgetcsv#57802
class CsvIterator implements Iterator
{
    const ROW_SIZE = 4096;
    /**
     * The pointer to the cvs file.
     * @var resource
     * @access private
     */
    private $filePointer = null;
    /**
     * The current element, which will 
     * be returned on each iteration.
     * @var array
     * @access private
     */
    private $currentElement = null;
    /**
     * The row counter. 
     * @var int
     * @access private
     */
    private $rowCounter = null;
    /**
     * The delimiter for the csv file. 
     * @var str
     * @access private
     */
    private $delimiter = null;

    /**
     * This is the constructor.It try to open the csv file.The method throws an exception
     * on failure.
     *
     * @access public
     * @param str $file The csv file.
     * @param str $delimiter The delimiter.
     *
     * @throws Exception
     */
    public function __construct($file, $delimiter=',')
    {
        try {
            $this->filePointer = fopen($file, 'r');
            $this->delimiter = $delimiter;
        }
        catch (Exception $e) {
            throw new Exception('The file "'.$file.'" cannot be read.');
        } 
    }

    /**
     * This method resets the file pointer.
     *
     * @access public
     */
    public function rewind() {
        $this->rowCounter = 0;
        rewind($this->filePointer);
    }

    /**
     * This method returns the current csv row as a 2 dimensional array
     *
     * @access public
     * @return array The current csv row as a 2 dimensional array
     */
    public function current() {
        $this->currentElement = fgetcsv($this->filePointer, self::ROW_SIZE, $this->delimiter);
        $this->rowCounter++; 
        return $this->currentElement;
    }

    /**
     * This method returns the current row number.
     *
     * @access public
     * @return int The current row number
     */
    public function key() {
        return $this->rowCounter;
    }

    /**
     * This method checks if the end of file is reached.
     *
     * @access public
     * @return boolean Returns true on EOF reached, false otherwise.
     */
    public function next() {
        return !feof($this->filePointer);
    }

    /**
     * This method checks if the next row is a valid row.
     *
     * @access public
     * @return boolean If the next row is a valid row.
     */
    public function valid() {
        if (!$this->next()) {
            fclose($this->filePointer);
            return false;
        }
        return true;
    }
}