<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingExtraModel extends pjAppModel
{
/**
 * The name of table's primary key. If PK is over 2 or more columns set this to boolean null
 *
 * @var string
 * @access public
 */
	var $primaryKey = 'id';
/**
 * The name of table associate with current model
 *
 * @var string
 * @access protected
 */
	var $table = 'bookings_extras';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'quantity', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingExtraModel($attr);
	}

    public function saveExtras($extra_arr, $booking_id, $return_id = null)
    {
        if(!is_array($extra_arr) || !$booking_id)
        {
            return false;
        }

        $extra_arr = array_filter($extra_arr);
        if(!empty($extra_arr))
        {
            $this->reset()->where('booking_id', $booking_id)->whereNotIn('extra_id', array_keys($extra_arr))->eraseAll();
            foreach($extra_arr as $extra_id => $quantity)
            {
                $inserted = $this
                    ->reset()
                    ->setAttributes(array(
                        'booking_id' => $booking_id,
                        'extra_id'   => $extra_id,
                        'quantity'   => $quantity,
                    ))
                    ->insert()
                    ->getInsertId();
                if($inserted === false)
                {
                    // Record already exists. Update quantity.
                    $this
                        ->reset()
                        ->where('booking_id', $booking_id)
                        ->where('extra_id', $extra_id)
                        ->limit(1)
                        ->modifyAll(array('quantity' => $quantity));
                }
            }

            if($return_id)
            {
                $this->reset()->where('booking_id', $return_id)->whereNotIn('extra_id', array_keys($extra_arr))->eraseAll();
                foreach($extra_arr as $extra_id => $quantity)
                {
                    $inserted = $this
                        ->reset()
                        ->setAttributes(array(
                            'booking_id' => $return_id,
                            'extra_id'   => $extra_id,
                            'quantity'   => $quantity,
                        ))
                        ->insert()->getInsertId();
                    if($inserted === false)
                    {
                        // Record already exists. Update quantity.
                        $this
                            ->reset()
                            ->where('booking_id', $return_id)
                            ->where('extra_id', $extra_id)
                            ->limit(1)
                            ->modifyAll(array('quantity' => $quantity));
                    }
                }
            }
        }
        else
        {
            $this->reset()->where('booking_id', $booking_id)->eraseAll();
            if($return_id)
            {
                $this->reset()->where('booking_id', $return_id)->eraseAll();
            }
        }

        return true;
    }
}
?>
