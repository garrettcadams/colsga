<?php
namespace WilokeListgoFunctionality\Framework\Payment\PayPal;


class ConvertPayPalStatusToWilokeStatus{
	public static function convert($status){
		$status = strtolower($status);

		switch ($status){
			case 'completed':
				$status = 'succeeded';
				break;
		}

		return $status;
	}
}