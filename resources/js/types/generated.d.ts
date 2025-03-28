declare namespace App.Data {
    export type InstanceData = {
        id: string;
        name: string;
        created_by: App.Data.UserData;
        status: App.Data.InstanceStatusData;
        started_at: any | null;
        stopped_at: any | null;
        suspended_at: any | null;
        created_at: any;
        updated_at: any;
    };
    export type InstanceStatusData = {
        status: App.Enums.InstanceStatusEnum;
        label: string;
        color: string;
    };
    export type UserData = {
        id: string;
        name: string;
        email: string;
        instances: any | null;
        role: App.Enums.RolesEnum;
    };
}
declare namespace App.Enums {
    export type InstanceStatusEnum = 'started' | 'stopped' | 'suspended';
    export type RolesEnum = 'admin' | 'user';
}
