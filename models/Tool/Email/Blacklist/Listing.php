<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    Element
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Model\Tool\Email\Blacklist;

use Pimcore\Model;

/**
 * @method \Pimcore\Model\Tool\Email\Blacklist\Listing\Dao getDao()
 * @method Model\Tool\Email\Blacklist[] load()
 * @method Model\Tool\Email\Blacklist current()
 */
class Listing extends Model\Listing\AbstractListing
{
    /**
     * @var array|null
     * @deprecated use getter/setter methods or $this->data
     */
    protected $items = null;

    public function __construct()
    {
        $this->items =& $this->data;
    }

    /**
     * @param $items
     *
     * @return $this
     */
    public function setItems($items)
    {
        return $this->setData($items);
    }

    /**
     * @return Model\Tool\Email\Blacklist[]
     */
    public function getItems()
    {
        return $this->getData();
    }
}
