import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, TouchableOpacity, Alert } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store';
import { useRouter } from 'expo-router';

export default function SettingsScreen() {
    const [instName, setInstName] = useState('');
    const router = useRouter();

    useEffect(() => {
        SecureStore.getItemAsync('institute_name').then(name => setInstName(name || 'Institute'));
    }, []);

    const handleLogout = async () => {
        Alert.alert('Logout', 'Are you sure you want to sign out?', [
            { text: 'Cancel', style: 'cancel' },
            {
                text: 'Logout',
                style: 'destructive',
                onPress: async () => {
                    await SecureStore.deleteItemAsync('api_key');
                    await SecureStore.deleteItemAsync('institute_name');
                    router.replace('/login');
                }
            }
        ]);
    };

    return (
        <View style={styles.container}>
            <View style={styles.profileBox}>
                <View style={styles.avatar}>
                    <Ionicons name="business" size={40} color="white" />
                </View>
                <Text style={styles.instName}>{instName}</Text>
                <Text style={styles.adminTag}>Administrator</Text>
            </View>

            <View style={styles.menu}>
                <TouchableOpacity style={styles.menuItem}>
                    <Ionicons name="document-text" size={24} color="#64748b" />
                    <Text style={styles.menuText}>Institute Profile</Text>
                    <Ionicons name="chevron-forward" size={20} color="#cbd5e1" />
                </TouchableOpacity>

                <TouchableOpacity style={styles.menuItem}>
                    <Ionicons name="lock-closed" size={24} color="#64748b" />
                    <Text style={styles.menuText}>Security Settings</Text>
                    <Ionicons name="chevron-forward" size={20} color="#cbd5e1" />
                </TouchableOpacity>

                <TouchableOpacity style={[styles.menuItem, { borderBottomWidth: 0 }]} onPress={handleLogout}>
                    <Ionicons name="log-out" size={24} color="#dc2626" />
                    <Text style={[styles.menuText, { color: '#dc2626' }]}>Sign Out</Text>
                </TouchableOpacity>
            </View>

            <Text style={styles.versionStyle}>FeeFlow App v1.0.0</Text>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#f8fafc',
    },
    profileBox: {
        padding: 30,
        alignItems: 'center',
        backgroundColor: '#fff',
        borderBottomWidth: 1,
        borderBottomColor: '#f1f5f9',
    },
    avatar: {
        width: 80,
        height: 80,
        borderRadius: 40,
        backgroundColor: '#dc2626',
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: 15,
    },
    instName: {
        fontSize: 20,
        fontWeight: '800',
        color: '#1e293b',
    },
    adminTag: {
        fontSize: 14,
        color: '#64748b',
        marginTop: 2,
    },
    menu: {
        marginTop: 20,
        backgroundColor: '#fff',
        borderTopWidth: 1,
        borderBottomWidth: 1,
        borderColor: '#f1f5f9',
    },
    menuItem: {
        flexDirection: 'row',
        alignItems: 'center',
        padding: 15,
        borderBottomWidth: 1,
        borderBottomColor: '#f1f5f9',
    },
    menuText: {
        flex: 1,
        marginLeft: 15,
        fontSize: 16,
        fontWeight: '600',
        color: '#334155',
    },
    versionStyle: {
        textAlign: 'center',
        marginTop: 30,
        color: '#94a3b8',
        fontSize: 12,
    }
});
