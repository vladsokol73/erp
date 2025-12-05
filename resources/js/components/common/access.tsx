"use client";

import { ReactNode } from "react";
import { usePage } from "@inertiajs/react";

interface PageProps {
    permissions_names: string[];
    roles_names?: string[];
    role_title?: string;
    [key: string]: any;
}

interface AccessProps {
    children: ReactNode;

    /** Роли: достаточно любой из списка */
    role?: string | string[];
    /** Пермишены: достаточно любого из списка */
    permission?: string | string[];

    /** Между ролями и пермишенами: AND | OR */
    strategy?: "AND" | "OR";

    /** Что показать, если доступа нет */
    fallback?: ReactNode;
}

const Access = ({
                    children,
                    role,
                    permission,
                    strategy = "OR",
                    fallback = null,
                }: AccessProps) => {
    const { permissions_names = [], roles_names = [], role_title } =
        usePage<PageProps>().props;

    const toArray = (v?: string | string[]): string[] =>
        !v ? [] : Array.isArray(v) ? v : [v];

    // нормализуем роли: берём массив или одиночную строку
    const userRoles: string[] = roles_names && roles_names.length > 0
        ? roles_names
        : role_title
            ? [role_title]
            : [];

    const needRoles = toArray(role);
    const needPerms = toArray(permission);

    const roleSpecified = needRoles.length > 0;
    const permSpecified = needPerms.length > 0;

    // ANY внутри группы
    const rolesOk = !roleSpecified || needRoles.some((r) => userRoles.includes(r));
    const permsOk =
        !permSpecified || needPerms.some((p) => permissions_names.includes(p));

    // Комбинация групп
    let show: boolean;
    if (roleSpecified && permSpecified) {
        show = strategy === "AND" ? rolesOk && permsOk : rolesOk || permsOk;
    } else if (roleSpecified) {
        show = rolesOk;
    } else if (permSpecified) {
        show = permsOk;
    } else {
        show = true;
    }

    return <>{show ? children : fallback}</>;
};

export default Access;
