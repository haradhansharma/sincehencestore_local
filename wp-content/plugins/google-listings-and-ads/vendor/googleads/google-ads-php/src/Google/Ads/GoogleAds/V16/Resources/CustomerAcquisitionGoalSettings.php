<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v16/resources/campaign_lifecycle_goal.proto

namespace Google\Ads\GoogleAds\V16\Resources;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The customer acquisition goal settings for the campaign.
 *
 * Generated from protobuf message <code>google.ads.googleads.v16.resources.CustomerAcquisitionGoalSettings</code>
 */
class CustomerAcquisitionGoalSettings extends \Google\Protobuf\Internal\Message
{
    /**
     * Output only. Customer acquisition optimization mode of this campaign.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v16.enums.CustomerAcquisitionOptimizationModeEnum.CustomerAcquisitionOptimizationMode optimization_mode = 1 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     */
    protected $optimization_mode = 0;
    /**
     * Output only. Campaign specific values for the customer acquisition goal.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v16.common.LifecycleGoalValueSettings value_settings = 2 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     */
    protected $value_settings = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $optimization_mode
     *           Output only. Customer acquisition optimization mode of this campaign.
     *     @type \Google\Ads\GoogleAds\V16\Common\LifecycleGoalValueSettings $value_settings
     *           Output only. Campaign specific values for the customer acquisition goal.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Ads\GoogleAds\V16\Resources\CampaignLifecycleGoal::initOnce();
        parent::__construct($data);
    }

    /**
     * Output only. Customer acquisition optimization mode of this campaign.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v16.enums.CustomerAcquisitionOptimizationModeEnum.CustomerAcquisitionOptimizationMode optimization_mode = 1 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @return int
     */
    public function getOptimizationMode()
    {
        return $this->optimization_mode;
    }

    /**
     * Output only. Customer acquisition optimization mode of this campaign.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v16.enums.CustomerAcquisitionOptimizationModeEnum.CustomerAcquisitionOptimizationMode optimization_mode = 1 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @param int $var
     * @return $this
     */
    public function setOptimizationMode($var)
    {
        GPBUtil::checkEnum($var, \Google\Ads\GoogleAds\V16\Enums\CustomerAcquisitionOptimizationModeEnum\CustomerAcquisitionOptimizationMode::class);
        $this->optimization_mode = $var;

        return $this;
    }

    /**
     * Output only. Campaign specific values for the customer acquisition goal.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v16.common.LifecycleGoalValueSettings value_settings = 2 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @return \Google\Ads\GoogleAds\V16\Common\LifecycleGoalValueSettings|null
     */
    public function getValueSettings()
    {
        return $this->value_settings;
    }

    public function hasValueSettings()
    {
        return isset($this->value_settings);
    }

    public function clearValueSettings()
    {
        unset($this->value_settings);
    }

    /**
     * Output only. Campaign specific values for the customer acquisition goal.
     *
     * Generated from protobuf field <code>.google.ads.googleads.v16.common.LifecycleGoalValueSettings value_settings = 2 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @param \Google\Ads\GoogleAds\V16\Common\LifecycleGoalValueSettings $var
     * @return $this
     */
    public function setValueSettings($var)
    {
        GPBUtil::checkMessage($var, \Google\Ads\GoogleAds\V16\Common\LifecycleGoalValueSettings::class);
        $this->value_settings = $var;

        return $this;
    }

}

