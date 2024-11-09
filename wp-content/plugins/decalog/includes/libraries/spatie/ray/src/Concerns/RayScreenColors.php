<?php

namespace DLSpatie\Ray\Concerns;

/** @mixin \DLSpatie\Ray\Ray */
trait RayScreenColors
{
    public function screenGreen(): self
    {
        return $this->screenColor('green');
    }

    public function screenOrange(): self
    {
        return $this->screenColor('orange');
    }

    public function screenRed(): self
    {
        return $this->screenColor('red');
    }

    public function screenPurple(): self
    {
        return $this->screenColor('purple');
    }

    public function screenBlue(): self
    {
        return $this->screenColor('blue');
    }

    public function screenGray(): self
    {
        return $this->screenColor('gray');
    }
}
