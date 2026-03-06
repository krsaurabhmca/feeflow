import { Tabs } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

export default function TabLayout() {
    return (
        <Tabs screenOptions={{
            tabBarActiveTintColor: '#dc2626',
            tabBarInactiveTintColor: '#94a3b8',
            headerStyle: { backgroundColor: '#fff' },
            headerTitleStyle: { fontWeight: '800' },
        }}>
            <Tabs.Screen
                name="index"
                options={{
                    title: 'Dashboard',
                    tabBarIcon: ({ color }) => <Ionicons name="apps" size={24} color={color} />,
                }}
            />
            <Tabs.Screen
                name="students"
                options={{
                    title: 'Students',
                    tabBarIcon: ({ color }) => <Ionicons name="people" size={24} color={color} />,
                }}
            />
            <Tabs.Screen
                name="collect"
                options={{
                    title: 'Collect Fee',
                    tabBarIcon: ({ color }) => <Ionicons name="card" size={24} color={color} />,
                }}
            />
            <Tabs.Screen
                name="settings"
                options={{
                    title: 'Settings',
                    tabBarIcon: ({ color }) => <Ionicons name="settings" size={24} color={color} />,
                }}
            />
        </Tabs>
    );
}
