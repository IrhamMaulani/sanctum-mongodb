<?php

namespace Shahroz\Sanctum;

use Illuminate\Support\Str;

trait HasApiTokens {
	/**
	 * The access token the user is using for the current request.
	 *
	 * @var \Shahroz\Sanctum\Contracts\HasAbilities
	 */
	protected $accessToken;

	/**
	 * Get the access tokens that belong to model.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function tokens() {
		return $this->morphMany(Sanctum::$personalAccessTokenModel, 'tokenable');
	}

	/**
	 * Determine if the current API token has a given scope.
	 *
	 * @param  string  $ability
	 * @return bool
	 */
	public function tokenCan(string $ability) {
		return $this->accessToken ? $this->accessToken->can($ability) : false;
	}

	/**
	 * Create a new personal access token for the user.
	 *
	 * @param  string  $uniqueId
	 * @param  array  $abilities
	 * @return \Shahroz\Sanctum\NewAccessToken
	 */
	public function createToken(string $uniqueId, array $abilities = ['*']) {
		$token = $this->tokens()->create([
			'uniqueId' => $uniqueId,
			'token' => hash('sha256', $plainTextToken = Str::random(config('sanctum.token_length', 80))),
			'abilities' => $abilities,
		]);

		return new NewAccessToken($token, $token->id . '|' . $plainTextToken);
	}

	/**
	 * Get the access token currently associated with the user.
	 *
	 * @return \Shahroz\Sanctum\Contracts\HasAbilities
	 */
	public function currentAccessToken() {
		return $this->accessToken;
	}

	/**
	 * Set the current access token for the user.
	 *
	 * @param  \Shahroz\Sanctum\Contracts\HasAbilities  $accessToken
	 * @return $this
	 */
	public function withAccessToken($accessToken) {
		$this->accessToken = $accessToken;

		return $this;
	}
}
