<?php

/**
 * Controller Test Case
 *
 * Provides some convenience methods for testing Laravel Controllers.
 *
 * @author  Joseph Wynn <joseph@wildlyinaccurate.com>
 */
abstract class ControllerTestCase extends PHPUnit_Framework_TestCase
{

	/**
	 * The Laravel session must be re-loaded before each test, otherwise
	 * the session state is retained across multiple tests.
	 */
	public function setUp()
	{
		// For the moment lets try and do this without sessions.
		//Session::load();
	}

	public function tearDown()
	{
		$this->clean_request();
	}

	/**
	 * Ensures we don't have problems with dirty requests.
	 */
	private function clean_request(){
        $request = \Laravel\Request::foundation()->request;

        $req_keys = $request->keys();
       
        foreach($req_keys as $key){
            $request->remove($key);
        }
    }

	/**
	 * Call a controller method.
	 *
	 * This is basically an alias for Laravel's Controller::call() with the
	 * option to specify a request method.
	 *
	 * @param	string	$destination
	 * @param	array	$parameters
	 * @param	string	$method
	 * @return	\Laravel\Response
	 */
	public function call($destination, $parameters = array(), $method = 'GET')
	{
		Request::foundation()->server->add(array(
			'REQUEST_METHOD' => $method,
		));

		return Controller::call($destination, $parameters);
	}

	/**
	 * Alias for call()
	 *
	 * @param	string	$destination
	 * @param	array	$parameters
	 * @return	\Laravel\Response
	 */
	public function get($destination, $parameters = array())
	{
		return $this->call($destination, $parameters, 'GET');
	}

	/**
	 * Make a POST request to a controller method
	 *
	 * @param	string	$destination
	 * @param	array	$post_data
	 * @param	array	$parameters
	 * @return	\Laravel\Response
	 */
	public function post($destination, $post_data, $parameters = array())
	{
		Request::foundation()->request->add($post_data);

		return $this->call($destination, $parameters, 'POST');
	}

	/**
	 * Convinience function for returning just the data array from a GET request.
	 * 
	 * @param	string	$destination
	 * @param	array	$parameters
	 * @param	string	$method
	 */
	public function get_data($destination, $parameters = array())
	{
		return $this->extract_data($this->call($destination, $parameters, 'GET'));
	}

	/**
	 * Helper function to extract data from a response object.
	 * 
	 * @param Laravel\Response $response The Laravel response object.
	 * @return array $data The data array.
	 */
	public function extract_data($response)
	{
		return $response->content->data['content']->data;
	}
}