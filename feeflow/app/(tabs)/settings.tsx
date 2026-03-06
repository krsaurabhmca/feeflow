import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, TouchableOpacity, Alert, TextInput } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store';
import { useRouter } from 'expo-router';
import { getCategories, addCategory } from '../../lib/api';

export default function SettingsScreen() {
    const [instName, setInstName] = useState('');
    const [categories, setCategories] = useState<any[]>([]);
    const [newCat, setNewCat] = useState('');
    const [loading, setLoading] = useState(false);
    const router = useRouter();

    useEffect(() => {
        SecureStore.getItemAsync('institute_name').then(name => setInstName(name || 'Institute'));
        loadCategories();
    }, []);

    const loadCategories = async () => {
        const resp = await getCategories();
        if (resp.status) setCategories(resp.data);
    };

    const handleAddCategory = async () => {
        if (!newCat) return;
        setLoading(true);
        const resp = await addCategory({ category_name: newCat });
        setLoading(true);
        if (resp.status) {
            setNewCat('');
            loadCategories();
            Alert.alert('Success', 'Category added');
        }
        setLoading(false);
    };

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
                <Text style={styles.sectionLabel}>Fee Categories</Text>
                <View style={styles.addBox}>
                    <TextInput
                        style={styles.smallInput}
                        placeholder="Add new category..."
                        value={newCat}
                        onChangeText={setNewCat}
                    />
                    <TouchableOpacity style={styles.addBtn} onPress={handleAddCategory} disabled={loading}>
                        <Ionicons name="add" size={24} color="white" />
                    </TouchableOpacity>
                </View>
                {categories.map((c, i) => (
                    <View key={i} style={styles.menuItem}>
                        <Ionicons name="list" size={20} color="#64748b" />
                        <Text style={styles.menuText}>{c.category_name}</Text>
                    </View>
                ))}
            </View>

            <View style={[styles.menu, { marginTop: 20 }]}>
                <Text style={styles.sectionLabel}>Account</Text>
                <TouchableOpacity style={styles.menuItem} onPress={handleLogout}>
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
    sectionLabel: {
        fontSize: 12,
        fontWeight: '700',
        color: '#94a3b8',
        textTransform: 'uppercase',
        paddingHorizontal: 15,
        paddingTop: 15,
        paddingBottom: 5,
    },
    addBox: {
        flexDirection: 'row',
        padding: 15,
        gap: 10,
    },
    smallInput: {
        flex: 1,
        backgroundColor: '#f1f5f9',
        borderRadius: 8,
        paddingHorizontal: 15,
        height: 44,
    },
    addBtn: {
        backgroundColor: '#dc2626',
        width: 44,
        height: 44,
        borderRadius: 8,
        alignItems: 'center',
        justifyContent: 'center',
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
