<?php

abstract class PhalanxConduitAPIMethod extends ConduitAPIMethod {

  final public function getApplication() {
    return PhabricatorApplication::getByClass(PhalanxApplication::class);
  }

}
