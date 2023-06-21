/**
 * @OA\Info(
 *     version="1.0",
 *     title="Example for response examples value"
 * )
 */

/**
 * @OA\PathItem(path="/api")
 */

 /**
 * @OA\Put(
 *     path="/users/{id}",
 *     summary="Updates a user",
 *     @OA\Parameter(
 *         description="Parameter with mutliple examples",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         @OA\Examples(example="int", value="1", summary="An int value."),
 *         @OA\Examples(example="uuid", value="0006faf6-7a61-426c-9034-579f2cfcfa83", summary="An UUID value."),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK"
 *     )
 * )
 */

 /**
 * @OA\Post(
 *     path="/users",
 *     summary="Adds a new user - with oneOf examples",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="id",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="phone",
 *                     oneOf={
 *                     	   @OA\Schema(type="string"),
 *                     	   @OA\Schema(type="integer"),
 *                     }
 *                 ),
 *                 example={"id": "a3fb6", "name": "Jessica Smith", "phone": 12345678}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/Result"),
 *                 @OA\Schema(type="boolean")
 *             },
 *             @OA\Examples(example="result", value={"success": true}, summary="An result object."),
 *             @OA\Examples(example="bool", value=false, summary="A boolean value."),
 *         )
 *     )
 * )
 */

 /**
 * @OA\Schema(
 *  schema="Result",
 *  title="Sample schema for using references",
 * 	@OA\Property(
 * 		property="status",
 * 		type="string"
 * 	),
 * 	@OA\Property(
 * 		property="error",
 * 		type="string"
 * 	)
 * )
 */


// athor
Which user ID should the client be assigned to? (Optional):
 > 3

 What should we name the client?:
 > Swagger Client

 Where should we redirect the request after authorization? [http://localhost/auth/callback]:
 > http://localhost/api/oauth2-callback

New client created successfully.
Client ID: 997669e0-95fa-45c3-9281-c7ec17c34633
Client secret: JF9DfwXXS1iZtlWbhGpnCYaNIAYIhguESfHONocM
