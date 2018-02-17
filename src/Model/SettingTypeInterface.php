<?php

namespace Hgabka\SettingsBundle\Model;

interface SettingTypeInterface
{
    public function getId();

    public function getName();

    public function getPriority();

    public function getFormType();

    public function getFormTypeOptions();

    public function transformValue($value);

    public function reverseTransformValue($value);

    public function getHtml($value);
}
