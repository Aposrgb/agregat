<?php

namespace App\Helper\EnumRoles;

enum UserRoles: string
{
    case ROLE_ADMIN = "ROLE_ADMIN";
    case ROLE_TEST_MODERATOR = "ROLE_TEST_MODERATOR";
    case ROLE_MODERATOR = "ROLE_MODERATOR";
    case ROLE_LECTURER = "ROLE_LECTURER";
    case ROLE_MEMBER_ASSOCIATION = "ROLE_MEMBER_ASSOCIATION";
    case ROLE_GRADUATE_STUDENT = "ROLE_GRADUATE_STUDENT";
    case ROLE_STUDENT = "ROLE_STUDENT";
    case ROLE_ENTRANT = "ROLE_ENTRANT";
    case ROLE_LISTENER = "ROLE_LISTENER";
    case ROLE_GUEST = "ROLE_GUEST";

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN->value,
            self::ROLE_LISTENER->value,
            self::ROLE_ENTRANT->value,
            self::ROLE_STUDENT->value,
            self::ROLE_GRADUATE_STUDENT->value,
            self::ROLE_MEMBER_ASSOCIATION->value,
            self::ROLE_TEST_MODERATOR->value,
            self::ROLE_MODERATOR->value,
            self::ROLE_LECTURER->value,
        ];
    }

    public function getRole(): string
    {
        return match ($this) {
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_LISTENER => 'Слушатель',
            self::ROLE_ENTRANT => 'Абитуриент',
            self::ROLE_STUDENT => 'Студент',
            self::ROLE_GRADUATE_STUDENT => 'Выпусник',
            self::ROLE_MEMBER_ASSOCIATION => 'Член ассоциации',
            self::ROLE_TEST_MODERATOR => 'Модератор тестов',
            self::ROLE_MODERATOR => 'Модератор',
            self::ROLE_LECTURER => 'Лектор',
            self::ROLE_GUEST => 'Пользователь зарегистрирован не полностью'
        };
    }

    public static function getSitesRoles(): array
    {
        return [
            self::ROLE_ENTRANT->value,
            self::ROLE_STUDENT->value,
            self::ROLE_GRADUATE_STUDENT->value,
            self::ROLE_MEMBER_ASSOCIATION->value,
            self::ROLE_LECTURER->value,
        ];
    }
}