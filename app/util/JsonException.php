<?php

    namespace util;

	use Nette\Application\Responses\JsonResponse;

    /** Serialized exception into json */
	class JsonException extends JsonResponse {

		public function __construct(\Exception $e) {
			parent::__construct(array(
				"exception" => 1,
				"message" => $e->getMessage(),
				"className" => get_class($e)
			));
		}

	}

?>
