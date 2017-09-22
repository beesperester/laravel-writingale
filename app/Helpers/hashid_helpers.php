<?php

/**
 * Create new hashids instance.
 *
 * @return Hashids
 */
function hashids()
{
    return new Hashids(env('HASHID_SALT', ''));
}

/**
 * Encode new hashid for arguments.
 *
 * @return string
 */
function hashid()
{
    return hashids()->encode(func_get_args());
}

/**
 * Decode hashid.
 *
 * @param string $id
 *
 * @return int
 */
function hashid_decode($id)
{
    return hashids()->decode($id);
}
