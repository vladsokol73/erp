import { ReactNode } from "react";
import { usePage } from "@inertiajs/react";

interface PageProps {
    permissions_names: string[];
    [key: string]: any;
}

interface PermissionProps {
    children: ReactNode;
    allow?: string[] | string;
    any?: string[] | string;
    fallback?: ReactNode;
}

const Permission = ({ children, allow, any, fallback = null }: PermissionProps) => {
    const { permissions_names } = usePage<PageProps>().props;
    
    const normalize = (value: string | string[] | undefined): string[] => {
        if (!value) return [];
        return Array.isArray(value) ? value : [value];
    };

    const allowList = normalize(allow);
    const anyList = normalize(any);

    const hasAll = allowList.length === 0 || allowList.every(p => permissions_names.includes(p));
    const hasAny = anyList.length === 0 || anyList.some(p => permissions_names.includes(p));

    const show = hasAll && (anyList.length === 0 || hasAny);

    return <>{show ? children : fallback}</>;
};

export default Permission;
