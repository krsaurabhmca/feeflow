import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, FlatList, ActivityIndicator, TouchableOpacity, RefreshControl } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { getFees } from '../lib/api';
import { Stack, useRouter } from 'expo-router';

export default function ReceiptsScreen() {
    const [receipts, setReceipts] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const router = useRouter();

    const loadReceipts = async () => {
        try {
            const response = await getFees();
            if (response.status) {
                setReceipts(response.data);
            }
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    useEffect(() => {
        loadReceipts();
    }, []);

    const onRefresh = () => {
        setRefreshing(true);
        loadReceipts();
    };

    const renderItem = ({ item }: { item: any }) => (
        <TouchableOpacity style={styles.card}>
            <View style={styles.iconBox}>
                <Ionicons name="receipt-outline" size={24} color="#dc2626" />
            </View>
            <View style={styles.info}>
                <Text style={styles.studentName}>{item.student_name}</Text>
                <Text style={styles.meta}>{item.receipt_no} • {item.payment_date}</Text>
                <Text style={styles.method}>{item.payment_method}</Text>
            </View>
            <View style={styles.right}>
                <Text style={styles.amount}>₹{item.amount}</Text>
                {item.remarks ? <Ionicons name="information-circle" size={16} color="#94a3b8" /> : null}
            </View>
        </TouchableOpacity>
    );

    return (
        <View style={styles.container}>
            <Stack.Screen options={{ title: 'All Receipts', headerShown: true }} />
            {loading ? (
                <ActivityIndicator size="large" color="#dc2626" style={{ marginTop: 20 }} />
            ) : (
                <FlatList
                    data={receipts}
                    keyExtractor={(item, index) => index.toString()}
                    renderItem={renderItem}
                    contentContainerStyle={styles.list}
                    refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
                    ListEmptyComponent={<Text style={styles.emptyText}>No receipts found</Text>}
                />
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#f8fafc',
    },
    list: {
        padding: 20,
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
    studentName: {
        fontSize: 16,
        fontWeight: '700',
        color: '#1e293b',
    },
    meta: {
        fontSize: 12,
        color: '#64748b',
        marginTop: 2,
    },
    method: {
        fontSize: 11,
        color: '#dc2626',
        fontWeight: '600',
        marginTop: 4,
        textTransform: 'uppercase',
    },
    right: {
        alignItems: 'flex-end',
    },
    amount: {
        fontSize: 16,
        fontWeight: '800',
        color: '#1e293b',
        marginBottom: 5,
    },
    emptyText: {
        textAlign: 'center',
        color: '#94a3b8',
        marginTop: 30,
    }
});
