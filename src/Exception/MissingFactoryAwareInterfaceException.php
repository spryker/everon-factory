<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Exception;

use Everon\Component\Utils\Exception\AbstractException;

class MissingFactoryAwareInterfaceException extends AbstractException
{

    protected $message = 'FactoryAwareInterface is not implemented in "%s"';

}
