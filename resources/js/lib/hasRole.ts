import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import RolesEnum = App.Enums.RolesEnum;

export function hasRole(role: RolesEnum): boolean {
    const page = usePage<SharedData>();

    return page.props.auth.user.role === role;
}
