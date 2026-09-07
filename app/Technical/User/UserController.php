<?php

namespace App\Technical\User;

use App\Domain\User\TokenService;
use App\Domain\User\User;
use App\Technical\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;

class UserController extends Controller
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    #[OA\POST(
        path: '/user/register',
        summary: 'register a new user account',
        requestBody: new OA\RequestBody(
            required: true,
            content: new JsonContent(
                required: ['username, password'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'john_doe'),
                    new OA\Property(property: 'password', type: 'string', example: 'my_pass321!'),
                    new OA\Property(property: 'first_name', type: 'string', example: 'John'),
                    new OA\Property(property: 'last_name', type: 'string', example: 'Doe'),
                ]
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User registered',
                content: new JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: '14|dJZuxaHIoPipXlyfLQjtSAsRzYLdz8ye11q9qrTS752eb1fb'),
                        new OA\Property(property: 'expires_at', type: 'string', example: '2026-09-03 17:07:20'),
                    ]
                )
            )
        ]
    )]
    public function register(RegisterUserDto $userDto): Response|JsonResponse
    {
        $user = new User([
            'username' => $userDto->getUsername(),
            'first_name' => $userDto->getFirstName(),
            'last_name' => $userDto->getLastName(),
            'password' => $userDto->getPassword(),
        ]);
        $userExistsAlready = User::query()->where('username', '=', "$user->username")->exists();
        if ($userExistsAlready) {
            return response(content: 'already exists', status: 400);
        }

        $user->save();
        $token = $this->tokenService->createToken($user);

        return response()->json(['token' => $token->plainTextToken, 'expires_at' => $token->accessToken->expires_at]);
    }

    public function login(LoginUserDto $userDto): Response|JsonResponse
    {
        $user = User::query()->where('username', '=', $userDto->getUsername())->first();
        if (null === $user) {
            return response(content: 'user not found', status: 404);
        }

        if ($user->checkPassword($userDto->getPassword())) {
            $token = $this->tokenService->createToken($user);
            return response()->json(['token' => $token->plainTextToken, 'expires_at' => $token->accessToken->expires_at]);
        }

        return response(status: 401);
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();
        return response(status: 204);
    }
}
