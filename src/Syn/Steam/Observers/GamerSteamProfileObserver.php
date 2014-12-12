<?php namespace Syn\Steam\Observers;

use Queue;

class GamerSteamProfileObserver
{
	/**
	 * New or saved model
	 * @param $model
	 */
	public function saved($model)
	{

		if($model -> isDirty('url'))
			Queue::push('Syn\Steam\Tasks\SteamProfileTask@readXmlProfile', [
				'id' => $model -> id
			]);
	}
}