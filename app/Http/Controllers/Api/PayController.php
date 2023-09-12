<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Stripe\Webhook;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\UnexpectedValueException;
use Stripe\Exception\SignatureVerificationException;

class PayController extends Controller
{
    //
    public function checkout(Request $request)
    {


        try {
            $user = $request->user();
            $token = $user->token;
            $courseId = $request->id;

            /*Stripe Api Key */
            Stripe::setApiKey('sk_test_51NU5DSJlz3vuQc0CsZEzZZ1XY0ENFkJJ8vIGawR9bsOwOdCJW5fP3alhtZq2DCteTURy0t5AYuziAuxbxJegt6Ti00Ns5JrglM');
            $courseResult = Course::where('id', '=', $courseId)->first();
            if (empty($courseResult)) {
                // invalid request
                return response()->json(
                    [
                        'code' => 400,
                        'msg' => "Course Does not Exsist",
                        'data' => '',
                    ],
                    400
                );
            }

            $orderMap = [];
            $orderMap['course_id'] = $courseId;
            $orderMap['user_token'] = $token;
            $orderMap['status'] = 1;

            /** if order has been placed before or not
             * so we need order model/table
             */
            $orderRes = Order::where($orderMap)->first();
            if (!empty($orderRes)) {
                return response()->json(
                    [
                        'code' => 400,
                        'msg' => "You already Bought this Course",
                        'data' => $orderRes
                    ],
                    400
                );
            }
            // new order for the usr lets submit
            $YOUR_DOMAIN = env('APP_URL');
            $map = [];
            $map['course_id'] = $courseId;
            $map['user_token'] = $token;
            $map['total_amount'] = $courseResult->price;
            $map['status'] = 0;
            $map['created_at'] = Carbon::now();
            $orderNum = Order::insertGetId($map);

            // create payment Session
            $checkOutSession = Session::create(
                [
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'USD',
                                'product_data' => [
                                    'name' => $courseResult->name,
                                    'description' => $courseResult->description
                                ],
                                'unit_amount' => intval(($courseResult->price) * 100),
                            ],
                            /**price_data */
                            'quantity' => 1,

                        ]
                    ],
                    /** line_items*/

                    'payment_intent_data' => [
                        'metadata' => [
                            'order_num' => $orderNum,
                            'user_token' => $token,

                        ],
                    ],
                    /** payment_intent_data*/
                    'metadata' => [
                        'order_num' => $orderNum,
                        'user_token' => $token,

                    ],
                    /** mete data */
                    'mode' => 'payment',
                    'success_url' => $YOUR_DOMAIN . 'success',
                    'cancel_url' => $YOUR_DOMAIN . 'cancel',
                ]
            );
            // returning strip url
            return response()->json(
                [
                    'code' => 200,
                    'msg' => "Success",
                    'data' => $checkOutSession->url
                ],
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'error' => $th
                ],
                500
            );
        }



    }
}