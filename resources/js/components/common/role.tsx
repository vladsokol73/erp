import { ReactNode } from "react";
import { usePage } from "@inertiajs/react";

interface PageProps {
    role_title: string;
    [key: string]: any;
}

interface RoleProps {
    children: ReactNode;
    is?: string | string[];
    oneOf?: string | string[];
    fallback?: ReactNode;
}

const Role = ({ children, is, oneOf, fallback = null }: RoleProps) => {
    const { role_title } = usePage<PageProps>().props;

    const normalize = (value: string | string[] | undefined): string[] => {
        if (!value) return [];
        return Array.isArray(value) ? value : [value];
    };

    const mustHave = normalize(is);
    const oneOfList = normalize(oneOf);

    const matchesAll = mustHave.length === 0 || mustHave.every(r => r === role_title);
    const matchesAny = oneOfList.length === 0 || oneOfList.includes(role_title);

    const show = matchesAll && (oneOfList.length === 0 || matchesAny);

    return <>{show ? children : fallback}</>;
};

export default Role;
