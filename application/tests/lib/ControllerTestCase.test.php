<?php

/**
 * Controller Test Case
 *
 * Provides some convenience methods for testing Laravel Controllers.
 *
 * @author  Joseph Wynn <joseph@wildlyinaccurate.com>
 */
abstract class ControllerTestCase extends BaseTestCase
{

	/**
	 * Populate the database.
	 * 
	 * The Laravel session must be re-loaded before each test, otherwise
	 * the session state is retained across multiple tests.
	 */
	public function setUp()
	{
		Tests\Helper::migrate();

		Session::load();
	}

	/**
	 * Ensures we don't have problems with dirty requests.
	 */
	protected static function clean_request(){
        $request = \Laravel\Request::foundation()->request;

        $req_keys = $request->keys();
       
        foreach($req_keys as $key){
            $request->remove($key);
        }

        $headers = \Laravel\Request::foundation()->headers;

        $header_keys = $headers->keys();
       
        foreach($header_keys as $key){
            $headers->remove($key);
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
	 * @param	array	$headers
	 * @return	\Laravel\Response
	 */
	public function call($destination, $parameters = array(), $method = 'GET', $headers = array())
	{
		Request::foundation()->setMethod($method);

		if(sizeof($headers) !== 0){
			foreach($headers as $header => $value) Request::foundation()->headers->set($header, $value);
		}
		
		return Controller::call($destination, $parameters);
	}

	/**
	 * Alias for call()
	 *
	 * @param	string	$destination
	 * @param	array	$parameters
	 * @param	array	$headers
	 * @return	\Laravel\Response
	 */
	public function get($destination, $parameters = array(), $headers = array())
	{
		return $this->call($destination, $parameters, 'GET', $headers);
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
	 * Given a controller path, returns the HTML.
	 * 
	 * @param string $controller_path
	 * @return string $html HTML result (including 404s and other errors)
	 */
	public function get_html($controller_path)
	{
		$page = $this->get($controller_path);

		return $page->render();
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

	/**
	 * Helper function to return the view name from a response object.
	 * 
	 * @param Laravel\Response $response The Laravel response object.
	 * @return string|bool Either the view, or if it is not present, false.
	 */
	public function get_view($response)
	{	
		try 
		{
			return $response->content->data['content']->view;
		} 
		catch (Exception $e) 
		{
			return false;
		}
	}

	/**
	 * Helper function to return the cleaned up location from a response object.
	 * 
	 * @param Laravel\Response $response The Laravel response object.
	 * @return The location, if any.
	 */
	public function get_location($response)
	{
		$headers = $response->headers();

		return substr(preg_replace('#^https?://#', '', $headers->get('location')), 1);
	}
}