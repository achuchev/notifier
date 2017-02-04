<?php

namespace Notifier\PolicyEngine\Then\Modification;

abstract class ModificationBase {
	abstract public function perform($messageDataList);
}