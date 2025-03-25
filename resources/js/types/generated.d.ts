declare namespace App.Data {
export type UserData = {
id: string;
name: string;
email: string;
role: App.Enums.RolesEnum;
};
}
declare namespace App.Enums {
export type RolesEnum = 'admin' | 'user';
}
