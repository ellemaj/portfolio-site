<?php

namespace App\Repositories;

use App\Models\Profile;
use Framework\Database;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function __construct(
        private Database $db
    ) {
    }

    public function get(): ?Profile
    {
        $data = $this->db
            ->run("SELECT * FROM profile LIMIT 1")
            ->fetch();

        if (!$data instanceof \stdClass) {
            return null;
        }

        return $this->mapToProfile($data);
    }

    public function update(Profile $profile): bool
    {
        $this->db->run("
            UPDATE profile SET
                intro      = :intro,
                bio        = :bio,
                birthdate  = :birthdate,
                education  = :education,
                experience = :experience,
                skills     = :skills,
                traits     = :traits,
                github     = :github,
                linkedin   = :linkedin,
                spotify    = :spotify,
                discord    = :discord,
                image      = :image
            WHERE id = :id
        ", [
            'id'         => $profile->id,
            'intro'      => $profile->intro,
            'bio'        => $profile->bio,
            'birthdate'  => $profile->birthdate,
            'education'  => $profile->education,
            'experience' => $profile->experience,
            'skills'     => $profile->skills,
            'traits'     => $profile->traits,
            'github'     => $profile->github,
            'linkedin'   => $profile->linkedin,
            'spotify'    => $profile->spotify,
            'discord'    => $profile->discord,
            'image'      => $profile->image,
        ]);

        return true;
    }

    private function mapToProfile(\stdClass $data): Profile
    {
        $profile = new Profile();

        $profile->id         = (int) $data->id;
        $profile->intro      = $data->intro;
        $profile->bio        = $data->bio;

        $profile->birthdate  = $data->birthdate ?? null;

        $profile->education  = $data->education ?? null;
        $profile->experience = $data->experience ?? null;

        $profile->skills     = $data->skills;
        $profile->traits     = $data->traits;

        $profile->github     = $data->github ?? null;
        $profile->linkedin   = $data->linkedin ?? null;
        $profile->spotify    = $data->spotify ?? null;
        $profile->discord    = $data->discord ?? null;

        $profile->image      = $data->image ?? null;

        return $profile;
    }
}
