import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, TextInput, TouchableOpacity, ScrollView, Alert, ActivityIndicator, FlatList } from 'react-native';
import { useRouter, Stack } from 'expo-router';
import { getClasses, addClass } from '../lib/api';
import { Ionicons } from '@expo/vector-icons';

export default function ManageClassesScreen() {
    const [classes, setClasses] = useState<any[]>([]);
    const [className, setClassName] = useState('');
    const [loading, setLoading] = useState(true);
    const [adding, setAdding] = useState(false);
    const router = useRouter();

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const response = await getClasses();
            if (response.status) {
                setClasses(response.data);
            }
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    const handleAdd = async () => {
        if (!className) return;

        setAdding(true);
        try {
            const response = await addClass({ class_name: className });
            if (response.status) {
                setClassName('');
                loadData();
                Alert.alert('Success', 'Class/Course added successfully');
            } else {
                Alert.alert('Error', response.message);
            }
        } catch (e) {
            Alert.alert('Error', 'Failed to add class');
        } finally {
            setAdding(false);
        }
    };

    const renderItem = ({ item }: { item: any }) => (
        <View style={styles.card}>
            <View style={styles.iconBox}>
                <Ionicons name="school" size={24} color="#dc2626" />
            </View>
            <View style={styles.info}>
                <Text style={styles.className}>{item.class_name}</Text>
            </View>
        </View>
    );

    return (
        <View style={styles.container}>
            <Stack.Screen options={{ title: 'Manage Classes', headerShown: true }} />

            <View style={styles.addSection}>
                <Text style={styles.label}>Add New Class / Course</Text>
                <View style={styles.inputGroup}>
                    <TextInput
                        style={styles.input}
                        placeholder="e.g. Class 10, BCA, Python 101"
                        value={className}
                        onChangeText={setClassName}
                    />
                    <TouchableOpacity
                        style={[styles.addBtn, adding && { opacity: 0.7 }]}
                        onPress={handleAdd}
                        disabled={adding}
                    >
                        {adding ? <ActivityIndicator color="white" /> : <Ionicons name="add" size={28} color="white" />}
                    </TouchableOpacity>
                </View>
            </View>

            <View style={styles.listSection}>
                <Text style={styles.sectionTitle}>Existing Classes</Text>
                {loading ? (
                    <ActivityIndicator color="#dc2626" />
                ) : (
                    <FlatList
                        data={classes}
                        keyExtractor={(item) => item.id.toString()}
                        renderItem={renderItem}
                        ListEmptyComponent={<Text style={styles.emptyText}>No classes added yet</Text>}
                    />
                )}
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#fff',
    },
    addSection: {
        padding: 20,
        backgroundColor: '#f8fafc',
        borderBottomWidth: 1,
        borderBottomColor: '#f1f5f9',
    },
    label: {
        fontWeight: '700',
        color: '#1e293b',
        fontSize: 14,
        marginBottom: 10,
    },
    inputGroup: {
        flexDirection: 'row',
        gap: 10,
    },
    input: {
        flex: 1,
        borderWidth: 1,
        borderColor: '#e2e8f0',
        borderRadius: 12,
        padding: 15,
        fontSize: 16,
        backgroundColor: '#fff',
    },
    addBtn: {
        backgroundColor: '#dc2626',
        width: 55,
        height: 55,
        borderRadius: 12,
        alignItems: 'center',
        justifyContent: 'center',
    },
    listSection: {
        flex: 1,
        padding: 20,
    },
    sectionTitle: {
        fontSize: 18,
        fontWeight: '700',
        color: '#1e293b',
        marginBottom: 15,
    },
    card: {
        flexDirection: 'row',
        alignItems: 'center',
        padding: 15,
        backgroundColor: '#fff',
        borderRadius: 16,
        marginBottom: 12,
        borderWidth: 1,
        borderColor: '#f1f5f9',
    },
    iconBox: {
        width: 44,
        height: 44,
        borderRadius: 12,
        backgroundColor: '#fee2e2',
        alignItems: 'center',
        justifyContent: 'center',
        marginRight: 15,
    },
    info: {
        flex: 1,
    },
    className: {
        fontSize: 16,
        fontWeight: '700',
        color: '#1e293b',
    },
    emptyText: {
        textAlign: 'center',
        color: '#94a3b8',
        marginTop: 30,
    }
});
